<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\invites\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

use DecodeLabs\Genesis;
use DecodeLabs\Metamorph;
use DecodeLabs\Tagged as Html;

class HttpResend extends arch\node\ConfirmForm
{
    public const ITEM_NAME = 'invite';

    protected $_invite;

    protected function init()
    {
        $this->_invite = $this->scaffold->getRecord();

        if (!$this->_invite['isActive']) {
            $this->comms->flashError(
                'invite.inactive',
                $this->_('This invite is no longer active')
            );

            return $this->complete();
        }
    }

    protected function createItemUi($container)
    {
        $container->push(
            $this->html->attributeList($this->_invite)
                ->addField('creationDate', function ($invite) {
                    return Html::$time->date($invite['creationDate']);
                })
                ->addField('name')
                ->addField('email', function ($invite) {
                    return $this->html->mailLink($invite['email']);
                })
                ->addField('message', function ($invite) {
                    return Metamorph::idiom($invite['message']);
                })
                ->addField('groups', function ($invite) {
                    return Html::uList($invite->groups->fetch(), function ($group) {
                        return $this->apex->component('../groups/GroupLink', $group);
                    });
                })
        );


        // Force send
        if (!Genesis::$environment->isProduction()) {
            $container->addField()->push(
                $this->html->checkbox('forceSend', $this->values->forceSend, $this->_(
                    'Force sending to recipient even in testing mode'
                ))
            );
        }
    }

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to resend this invite?');
    }

    protected function customizeMainButton($button)
    {
        $button->setBody($this->_('Resend'))
            ->setIcon('refresh');
    }

    protected function apply()
    {
        $validator = $this->data->newValidator()
            ->addRequiredField('forceSend', 'boolean')
            ->validate($this->values);

        if ($validator['forceSend']) {
            $this->data->user->invite->forceResend($this->_invite);
        } else {
            $this->data->user->invite->resend($this->_invite);
        }
    }

    protected function getFlashMessage()
    {
        return $this->_('Your invite has been successfully resent');
    }
}
