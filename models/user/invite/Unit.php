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
use df\flow;

class Unit extends axis\unit\table\Base {
    
    const INVITE_OPTION = 'invite.allowance';

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

        $schema->addField('isFromAdmin', 'Boolean');

        $schema->addField('customData', 'DataObject')
            ->isNullable(true);

        $schema->addField('lastSent', 'DateTime');
        $schema->addField('isActive', 'Boolean')
            ->setDefaultValue(true);
    }

    public function applyPagination(opal\query\IPaginator $paginator) {
        $paginator
            ->setOrderableFields(
                'creationDate', 'owner', 'name', 'email', 
                'registrationDate', 'user', 'lastSent'
            )
            ->setDefaultOrder('lastSent DESC');

        return $this;
    }


    public function emailIsActive($email) {
        return (bool)$this->select()->where('email', '=', $email)->where('isActive', '=', true)->count();
    }

    public function sendAsAllowance(Record $invite, $rendererPath=null, $rendererLocation=null) {
        return $this->_send($invite, $rendererPath, $rendererLocation, true);
    }

    public function send(Record $invite, $rendererPath=null, $rendererLocation=null) {
        return $this->_send($invite, $rendererPath, $rendererLocation, false);
    }

    protected function _send(Record $invite, $rendererPath=null, $rendererLocation=null, $allowance=false) {
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

        if(!$invite['owner']) {
            $invite['owner'] = $this->context->user->client->getId();
        }

        $ownerId = $invite->getRawId('owner');
        $isClient = $ownerId == $this->context->user->client->getId();
        $model = $this->getModel();

        if($isClient && $allowance && $this->context->user->canAccess('virtual://unlimited-invites')) {
            $allowance = false;
        }

        if($allowance) {
            if($isClient) {
                $userCap = $this->getClientAllowance();
            } else {
                $userCap = $this->getUserAllowance($ownerId);
            }

            if($userCap !== null && $userCap <= 0) {
                throw new \RuntimeException(
                    'User has no more invite allowance'
                );
            }
        }

        $invite['key'] = core\string\Generator::sessionId();
        $invite['isActive'] = true;

        if($rendererPath === null) {
            $rendererPath = 'users/Invite';
        }

        if($rendererLocation !== null) {
            $this->context->comms->templateNotify(
                $rendererPath,
                $rendererLocation,
                ['invite' => $invite],
                flow\mail\Address::factory($invite['email'], $invite['name'])
            );
        } else {
            $this->context->comms->componentNotify(
                $rendererPath,
                [$invite],
                flow\mail\Address::factory($invite['email'], $invite['name'])
            );
        }

        $invite['lastSent'] = 'now';
        $invite->save();

        if($allowance) {
            $userCap--;

            if($userCap < 0) {
                $userCap = 0;
            }

            if($isClient) {
                $this->context->user->setClientOption(self::INVITE_OPTION, $userCap);
            } else {
                $model->option->setOption($ownerId, self::INVITE_OPTION, $userCap);
            }
        }

        $this->context->mesh->emitEvent($invite, 'send');
        return $invite;
    }

    public function resend(Record $invite, $rendererPath=null, $rendererLocation=null) {
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

        if($rendererPath === null) {
            $rendererPath = 'users/Invite';
        }

        if($rendererLocation !== null) {
            $this->context->comms->templateNotify(
                $rendererPath,
                $rendererLocation,
                ['invite' => $invite],
                flow\mail\Address::factory($invite['email'], $invite['name'])
            );
        } else {
            $this->context->comms->componentNotify(
                $rendererPath,
                [$invite],
                flow\mail\Address::factory($invite['email'], $invite['name'])
            );
        }

        $invite['lastSent'] = 'now';
        $invite->save();

        $this->context->mesh->emitEvent($invite, 'send');
        return $invite;
    }

    public function ensureSent($email, Callable $generator, $rendererPath=null, $rendererLocation=null) {
        $invite = $this->fetch()
            ->where('email', '=', $email)
            ->where('registrationDate', '=', null)
            ->where('isActive', '=', true)
            ->toRow();

        if($invite) {
            call_user_func($generator, $invite);
            $this->resend($invite, $rendererPath, $rendererLocation);
            return $this;
        }

        $invite = $this->newRecord([
            'email' => $email
        ]);

        call_user_func($generator, $invite);
        $this->send($invite, $rendererPath, $rendererLocation);
        return $this;
    }

    public function claim(Record $invite, user\IClientDataObject $client) {
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

    public function getClientAllowance() {
        $cap = $this->getClientCap();

        if(!$cap || $this->context->user->canAccess('virtual://unlimited-invites')) {
            return null;
        }
        
        $output = $this->context->user->getClientOption(self::INVITE_OPTION);

        if($output !== null) {
            return $output;
        }

        return $cap;
    }

    public function getUserAllowance($userId) {
        $cap = $this->getUserCap($userId);

        if(!$cap) {
            return null;
        }

        $model = $this->getModel();
        $output = $model->option->fetchOption($userId, self::INVITE_OPTION);

        if($output !== null) {
            return $output;
        }

        return $cap;
    }

    public function getClientCap() {
        if(!$this->_model->config->hasInviteCap()) {
            return null;
        }

        return $this->_getCap(
            $this->context->user->client->getGroupIds()
        );
    }

    public function getUserCap($userId) {
        if(!$this->_model->config->hasInviteCap()) {
            return null;
        }

        return $this->_getCap(
            $this->_model->client->getBridgeUnit('groups')->select('group')
                ->where('client', '=', $userId)
                ->toList('group')
        );
    }

    protected function _getCap(array $groupIds) {
        $config = $this->_model->config;
        $cap = $config->getInviteCap();
        $groupCaps = $config->getInviteGroupCaps();

        if(!empty($groupCaps)) {
            $groupCap = 0;

            foreach($groupIds as $groupId) {
                if(isset($groupCaps[$groupId]) && $groupCaps[$groupId] > $groupCap) {
                    $groupCap = $cap = $groupCaps[$groupId];
                }
            }
        }

        return $cap;
    }


    public function grantAllowance(array $userIds, $allowance) {
        $this->getModel()->option->setOptionForMany($userIds, self::INVITE_OPTION, (int)$allowance);
        return $this;
    }

    public function grantAllAllowance($allowance) {
        $this->getModel()->option->updateOptionForAll(self::INVITE_OPTION, (int)$allowance);
        return $this;
    }
}
