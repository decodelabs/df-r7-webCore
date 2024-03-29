<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\invites\_nodes;

use DecodeLabs\Metamorph;

use DecodeLabs\R7\Legacy;
use DecodeLabs\Tagged as Html;
use df\arch;

class HttpDeactivate extends arch\node\ConfirmForm
{
    public const ITEM_NAME = 'invite';
    public const DISPOSITION = 'negative';

    protected $_invite;

    protected function init(): void
    {
        $this->_invite = $this->scaffold->getRecord();

        if (!$this->_invite['isActive']) {
            $this->comms->flashError(
                'invite.inactive',
                $this->_('This invite is no longer active')
            );

            throw $this->forceResponse(
                Legacy::$http->defaultRedirect()
            );
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
    }

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to deactivate this invite?');
    }

    protected function customizeMainButton($button)
    {
        $button->setBody($this->_('Deactivate'))
            ->setIcon('remove');
    }

    protected function apply()
    {
        $this->_invite->deactivate();
    }

    protected function getFlashMessage()
    {
        return $this->_('Your invite has been successfully deactivated');
    }
}
