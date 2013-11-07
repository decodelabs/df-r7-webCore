<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\invites\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class InviteList extends arch\component\template\CollectionList {

    protected $_fields = [
        'creationDate' => true,
        'name' => true,
        'email' => true,
        'lastSent' => true,
        'owner' => true,
        'registrationDate' => true,
        'groups' => true,
        'actions' => true
    ];

// Creation date
    public function addCreationDateField($list) {
        $list->addField('creationDate', $this->_('Created'), function($invite, $context) {
            if(!$invite['isActive'] && !$invite['registrationDate']) {
                $context->getRowTag()->addClass('state-lowPriority');
            }

            return $this->import->component('InviteLink', '~admin/users/invites/', $invite)
                ->shouldUseDate(true)
                ->setIcon($invite['registrationDate'] ? 'tick' : 'mail')
                ->setDisposition($invite['registrationDate'] ? 'positive' : 'informative');
        });
    }

// Name
    public function addNameField($list) {
        $list->addField('name', function($invite) {
            if($invite['user']) {
                return $this->import->component('UserLink', '~admin/users/clients/', $invite['user']);
            } else {
                return $invite['name'];
            }
        });
    }

// Email
    public function addEmailField($list) {
        $list->addField('email', function($invite) {
            return $this->html->mailLink($invite['email']);
        });
    }

// Last sent
    public function addLastSentField($list) {
        $list->addField('lastSent', function($invite) {
            return $this->html->date($invite['lastSent']);
        });
    }

// Onwer
    public function addOwnerField($list) {
        $list->addField('owner', $this->_('Sent by'), function($invite) {
            return $this->import->component('UserLink', '~admin/users/clients/', $invite['owner']);
        });
    }

// Registration date
    public function addRegistrationDateField($list) {
        $list->addField('registrationDate', $this->_('Registered'), function($invite) {
            return $this->html->date($invite['registrationDate']);
        });
    }

// Groups
    public function addGroupsField($list) {
        $list->addField('groups', function($invite) {
            return $this->html->bulletList($invite['groups'], function($group) {
                return $this->import->component('GroupLink', '~admin/users/groups/', $group);
            });
        });
    }

// Actions
    public function addActionsField($list) {
        $list->addField('actions', function($invite) {
            return [
                // Resend
                $this->import->component('InviteLink', '~admin/users/invites/', $invite, $this->_('Resend'))
                    ->setAction('resend')
                    ->setIcon('refresh')
                    ->setDisposition('positive')
                    ->isDisabled(!$invite['isActive'] || $invite['registrationDate']),

                // Deactivate
                $this->import->component('InviteLink', '~admin/users/invites/', $invite, $this->_('Deactivate'))
                    ->setAction('deactivate')
                    ->setIcon('remove')
                    ->setDisposition('negative')
                    ->isDisabled(!$invite['isActive'])
            ];
        });
    }
}