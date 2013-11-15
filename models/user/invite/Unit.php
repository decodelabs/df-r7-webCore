<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\user\invite;

use df;
use df\core;
use df\axis;
use df\opal;
use df\user;

class Unit extends axis\unit\table\Base {
    
    protected function _onCreate(axis\schema\ISchema $schema) {
        $schema->addPrimaryField('id', 'AutoId');
        $schema->addUniqueField('key', 'String', 64);

        $schema->addField('creationDate', 'Timestamp');
        $schema->addField('owner', 'One', 'user/client');

        $schema->addField('name', 'String', 255);
        $schema->addField('email', 'String', 255);
        $schema->addField('message', 'BigString', 'medium')
            ->isNullable(true);

        $schema->addField('groups', 'Many', 'user/group')
            ->isNullable(true);

        $schema->addField('registrationDate', 'DateTime')
            ->isNullable(true);
        $schema->addUniqueField('user', 'One', 'user/client')
            ->isNullable(true);

        $schema->addField('customData', 'DataObject')
            ->isNullable(true);

        $schema->addField('lastSent', 'DateTime');
        $schema->addField('isActive', 'Boolean')
            ->setDefaultValue(true);
    }

    public function applyPagination(opal\query\IPaginator $paginator) {
        $paginator
            ->setOrderableFields('creationDate', 'owner', 'name', 'email', 'registrationDate', 'user')
            ->setDefaultOrder('creationDate DESC');

        return $this;
    }


    public function emailIsActive($email) {
        return (bool)$this->select()->where('email', '=', $email)->where('isActive', '=', true)->count();
    }

    public function send(Record $invite, $templatePath=null, $templateLocation=null) {
        if(!$invite->isNew()) {
            throw new \RuntimeException(
                'Invite has already been sent'
            );
        }

        if(!$invite['name'] || !$invite['email']) {
            throw new \RuntimeException(
                'Invite details are invalid'
            );
        }

        $invite['key'] = core\string\Generator::sessionId();

        if(!$invite['owner']) {
            $invite['owner'] = $this->context->user->client->getId();
        }

        $invite['isActive'] = true;

        if($templatePath === null) {
            $templatePath = 'messages/Invite.notification';
        }

        if($templateLocation === null) {
            $templateLocation = '~shared/users/invites/';
        }

        $this->context->comms->templateNotify(
            $templatePath,
            $templateLocation,
            ['invite' => $invite],
            $invite['email']
        );

        $invite['lastSent'] = 'now';
        $invite->save();

        $this->context->policy->triggerEntityEvent($invite, 'send');
        return $invite;
    }

    public function resend(Record $invite, $templatePath=null, $templateLocation=null) {
        if($invite->isNew()) {
            throw new \RuntimeException(
                'Invite has not been initialized'
            );
        }

        if(!$invite['name'] || !$invite['email'] || !$invite['key']) {
            throw new \RuntimeException(
                'Invite details are invalid'
            );
        }

        if(!$invite['isActive']) {
            throw new \RuntimeException(
                'Invite is no longer active'
            );
        }

        if(!$invite['owner']) {
            $invite['owner'] = $this->context->user->client->getId();
        }

        if($templatePath === null) {
            $templatePath = 'messages/Invite.notification';
        }

        if($templateLocation === null) {
            $templateLocation = '~shared/users/invites/';
        }

        $this->context->comms->templateNotify(
            $templatePath,
            $templateLocation,
            ['invite' => $invite],
            $invite['email']
        );

        $invite['lastSent'] = 'now';
        $invite->save();

        $this->context->policy->triggerEntityEvent($invite, 'send');
        return $invite;
    }

    public function claim(Record $invite, user\IClientDataObject $client) {
        $invite->registrationDate = 'now';
        $invite->user = $client->getId();
        $invite->isActive = false;
        $invite->save();

        $this->update(['isActive' => false])
            ->where('email', '=', $invite['email'])
            ->execute();

        $this->context->policy->triggerEntityEvent($invite, 'claim');
        return $this;
    }
}
