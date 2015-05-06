<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\invites\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpResend extends arch\form\template\Confirm {

    const ITEM_NAME = 'invite';

    protected $_invite;

    protected function _init() {
        $this->_invite = $this->scaffold->getRecord();

        if(!$this->_invite['isActive']) {
            $this->comms->flashError(
                'invite.inactive',
                $this->_('This invite is no longer active')
            );

            return $this->complete();
        }
    }

    protected function _renderItemDetails($container) {
        $container->push(
            $this->html->attributeList($this->_invite)
                ->addField('creationDate', function($invite) {
                    return $this->html->date($invite['creationDate']);
                })
                ->addField('name')
                ->addField('email', function($invite) {
                    return $this->html->mailLink($invite['email']);
                })
                ->addField('message', function($invite) {
                    return $this->html->simpleTags($invite['message']);
                })
                ->addField('groups', function($invite) {
                    return $this->html->bulletList($invite->groups->fetch(), function($group) {
                        return $this->apex->component('../groups/GroupLink', $group);
                    });
                })
        );
    }

    protected function _getMainMessage($itemName) {
        return $this->_('Are you sure you want to resend this invite?');
    }

    protected function _getMainButtonText() {
        return $this->_('Resend');
    }

    protected function _getMainButtonIcon() {
        return 'refresh';
    }

    protected function _apply() {
        $this->data->user->invite->resend($this->_invite);
    }

    protected function _getFlashMessage() {
        return $this->_('Your invite has been successfully resent');
    }
}