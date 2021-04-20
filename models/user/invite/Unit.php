<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\user\invite;

use df;
use df\core;
use df\axis;
use df\user;
use df\flow;
use df\flex;

use DecodeLabs\Disciple;
use DecodeLabs\Exceptional;

class Unit extends axis\unit\Table
{
    const INVITE_OPTION = 'invite.allowance';

    const SEARCH_FIELDS = [
        'name' => 5,
        'email' => 2
    ];

    const ORDERABLE_FIELDS = [
        'creationDate', 'owner', 'name', 'email',
        'registrationDate', 'user', 'lastSent'
    ];

    const DEFAULT_ORDER = 'lastSent DESC';

    protected function createSchema($schema)
    {
        $schema->addPrimaryField('id', 'AutoId');
        $schema->addUniqueField('key', 'Text', 64);

        $schema->addField('creationDate', 'Timestamp');
        $schema->addField('owner', 'One', 'user/client');

        $schema->addField('name', 'Text', 255);
        $schema->addField('email', 'Text', 255);
        $schema->addField('message', 'Text', 'medium')
            ->isNullable(true);

        $schema->addField('groups', 'Many', 'user/group')
            ->isNullable(true);

        $schema->addField('registrationDate', 'Date:Time')
            ->isNullable(true);
        $schema->addUniqueField('user', 'One', 'user/client')
            ->isNullable(true);

        $schema->addField('isFromAdmin', 'Boolean');

        $schema->addField('customData', 'DataObject')
            ->isNullable(true);

        $schema->addField('lastSent', 'Date:Time');
        $schema->addField('isActive', 'Boolean')
            ->setDefaultValue(true);
    }


    public function emailIsActive($email)
    {
        return (bool)$this->select()->where('email', '=', $email)->where('isActive', '=', true)->count();
    }

    public function send(Record $invite, $rendererPath=null)
    {
        return $this->_send($invite, $rendererPath);
    }

    public function forceSend(Record $invite, $rendererPath=null)
    {
        return $this->_send($invite, $rendererPath, true);
    }

    protected function _send(Record $invite, $rendererPath=null, $force=false)
    {
        if ($invite['lastSent']) {
            throw Exceptional::Runtime(
                'Invite has already been sent'
            );
        }

        if (!$invite['name'] || !$invite['email']) {
            throw Exceptional::Runtime(
                'Invite details are invalid'
            );
        }

        if (!$invite['#owner']) {
            $invite['owner'] = Disciple::getId();
        }

        $ownerId = $invite['#owner'];
        $isClient = $ownerId == Disciple::getId();
        $model = $this->getModel();

        if (!$invite['key']) {
            $invite['key'] = flex\Generator::sessionId();
        }

        $invite['isActive'] = true;

        if ($rendererPath === null) {
            $rendererPath = 'account/Invite';
        }

        $this->context->comms->prepareMail($rendererPath, ['invite' => $invite], $force)
            ->addToAddress($invite['email'], $invite['name'])
            ->send();

        $invite['lastSent'] = 'now';
        $invite->save();

        $this->context->mesh->emitEvent($invite, 'send');
        return $invite;
    }

    public function resend(Record $invite, $rendererPath=null)
    {
        return $this->_resend($invite, $rendererPath);
    }

    public function forceResend(Record $invite, $rendererPath=null)
    {
        return $this->_resend($invite, $rendererPath, true);
    }

    protected function _resend(Record $invite, $rendererPath=null, $force=false)
    {
        if ($invite->isNew()) {
            throw Exceptional::Runtime(
                'Invite has not been initialized'
            );
        }

        if (!$invite['name'] || !$invite['email'] || !$invite['key']) {
            throw Exceptional::Runtime(
                'Invite details are invalid'
            );
        }

        if (!$invite['isActive']) {
            throw Exceptional::Runtime(
                'Invite is no longer active'
            );
        }

        if (!$invite['owner']) {
            $invite['owner'] = Disciple::getId();
        }

        if ($rendererPath === null) {
            $rendererPath = 'account/Invite';
        }

        $this->context->comms->prepareMail($rendererPath, ['invite' => $invite], $force)
            ->addToAddress($invite['email'], $invite['name'])
            ->send();

        $invite['lastSent'] = 'now';
        $invite->save();

        $this->context->mesh->emitEvent($invite, 'send');
        return $invite;
    }

    public function ensureSent($email, $generator, $rendererPath=null)
    {
        $invite = $this->fetch()
            ->where('email', '=', $email)
            ->where('registrationDate', '=', null)
            ->where('isActive', '=', true)
            ->toRow();

        $generator = core\lang\Callback::factory($generator);

        if ($invite) {
            $generator->invoke($invite);
            $this->resend($invite, $rendererPath);
            return $this;
        }

        $invite = $this->newRecord([
            'email' => $email
        ]);

        $generator->invoke($invite);
        $this->send($invite, $rendererPath);
        return $this;
    }

    public function claim(Record $invite, user\IClientDataObject $client)
    {
        $this->update(['user' => null])
            ->where('user', '=', $client->getId())
            ->execute();

        $invite->registrationDate = 'now';
        $invite->user = $client->getId();
        $invite->isActive = false;
        $invite->save();

        $this->update(['isActive' => false])
            ->where('email', '=', $invite['email'])
            ->execute();

        $this->context->mesh->emitEvent($invite, 'claim');
        return $this;
    }
}
