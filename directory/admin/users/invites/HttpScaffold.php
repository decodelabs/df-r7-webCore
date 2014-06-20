<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\invites;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DIRECTORY_TITLE = 'Invites';
    const DIRECTORY_ICON = 'mail';
    const RECORD_ADAPTER = 'axis://user/Invite';
    const RECORD_NAME_KEY = 'creationDate';

    const CAN_ADD_RECORD = false;
    const CAN_EDIT_RECORD = false;
    const CAN_DELETE_RECORD = false;


    protected $_recordListFields = [
        'creationDate' => true,
        'name' => true,
        'email' => true,
        'lastSent' => true,
        'owner' => true,
        'registrationDate' => true,
        'groups' => true,
        'actions' => true
    ];

    protected $_recordDetailsFields = [
        'key' => true,
        'creationDate' => true,
        'owner' => true,
        'lastSent' => true,
        'name' => true,
        'email' => true,
        'message' => true,
        'groups' => true,
        'registrationDate' => true,
        'user' => true
    ];

// Record data
    protected function _prepareRecordListQuery(opal\query\ISelectQuery $query, $mode) {
        $query
            ->populateSelect('groups', 'id', 'name')
            ->importRelationBlock('owner', 'link')
            ->importRelationBlock('user', 'link');
    }

    public function applyRecordQuerySearch(opal\query\ISelectQuery $query, $search, $mode) {
        $query->beginWhereClause()
            ->whereCorrelation('user', 'in', 'id')
                ->from('axis://user/Client', 'client')
                ->where('client.fullName', 'matches', $search)
                ->orWhere('client.nickName', 'matches', $search)
                ->orWhere('client.email', 'matches', $search)
                ->endCorrelation()
            ->orWhere('invite.name', 'matches', $search)
            ->orWhere('invite.email', 'matches', $search)
            ->endClause();
    }


// Components
    public function addIndexOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~admin/users/invites/send', true),
                    $this->_('Invite new user')
                )
                ->setIcon('add')
                ->addAccessLock('axis://user/Invite#add')
        );
    }

    public function addIndexSubOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~admin/users/invites/grant', true),
                    $this->_('Grant allowance')
                )
                ->setIcon('edit'),

            $this->html->link(
                    $this->uri->request('~admin/users/settings', true),
                    $this->_('Settings')
                )
                ->setIcon('settings')
                ->setDisposition('operative')
        );
    }

    public function addIndexTransitiveLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    '~admin/users/invite-requests/',
                    $this->_('Invite requests')
                )
                ->setIcon('key')
                ->setDisposition('transitive')
        );
    }

    public function getRecordOperativeLinks($invite, $mode) {
        return [
            // Resend
            $this->import->component('InviteLink', '~admin/users/invites/', $invite, $this->_('Resend invite'))
                ->setAction('resend')
                ->setIcon('refresh')
                ->setDisposition('positive')
                ->isDisabled(!$invite['isActive'] || $invite['registrationDate']),

            // Deactivate
            $this->import->component('InviteLink', '~admin/users/invites/', $invite, $this->_('Deactivate invite'))
                ->setAction('deactivate')
                ->setIcon('remove')
                ->setDisposition('negative')
                ->isDisabled(!$invite['isActive'])
        ];
    }


// Sections
    public function renderDetailsSectionBody($invite) {
        $output = parent::renderDetailsSectionBody($invite);

        if(!$invite['isActive']) {
            $output = [
                $this->html->flashMessage($this->_(
                    'This invite is no longer active'
                ), 'warning'),
                $output
            ];
        }

        return $output;
    }


// Fields
    public function defineCreationDateField($list, $mode) {
        if($mode != 'list') {
            return false;
        }

        $list->addField('creationDate', $this->_('Created'), function($invite, $context) {
            if(!$invite['isActive'] && !$invite['registrationDate']) {
                $context->getRowTag()->addClass('state-lowPriority');
            }

            return $this->import->component('InviteLink', '~admin/users/invites/', $invite, $this->format->date($invite['creationDate'], 'short'))
                ->setIcon($invite['registrationDate'] ? 'tick' : 'mail')
                ->setDisposition($invite['registrationDate'] ? 'positive' : 'informative');
        });
    }

    public function defineNameField($list) {
        $list->addField('name', function($invite) {
            if($invite['user']) {
                return $this->import->component('UserLink', '~admin/users/clients/', $invite['user']);
            } else {
                return $invite['name'];
            }
        });
    }

    public function defineLastSentField($list, $mode) {
        $list->addField('lastSent', function($invite) use($mode) {
            if($mode == 'list') {
                return $this->html->date($invite['lastSent']);
            } else {
                return $this->html->userDateTime($invite['lastSent']);
            }
        });
    }

    public function defineOwnerField($list, $mode) {
        $list->addField('owner', $this->_('Sent by'), function($invite) {
            return $this->import->component('UserLink', '~admin/users/clients/', $invite['owner'])
                ->setDisposition('transitive');
        });
    }

    public function defineRegistrationDateField($list) {
        $list->addField('registrationDate', $this->_('Registered'), function($invite) {
            return $this->html->date($invite['registrationDate']);
        });
    }

    public function defineMessageField($list) {
        $list->addField('message', function($invite) {
            return $this->html->simpleTags($invite['message']);
        });
    }

    public function defineGroupsField($list, $mode) {
        $list->addField('groups', function($invite) use($mode) {
            if($mode == 'list') {
                $groups = $invite['groups'];
            } else {
                $groups = $invite->groups->select();
            }

            return $this->html->bulletList($groups, function($group) {
                return $this->import->component('GroupLink', '~admin/users/groups/', $group);
            });
        });
    }
}