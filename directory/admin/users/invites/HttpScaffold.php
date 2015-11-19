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

class HttpScaffold extends arch\scaffold\RecordAdmin {

    const DIRECTORY_TITLE = 'Invites';
    const DIRECTORY_ICON = 'mail';
    const RECORD_ADAPTER = 'axis://user/Invite';
    const RECORD_NAME_FIELD = 'email';

    const CAN_ADD_RECORD = false;
    const CAN_EDIT_RECORD = false;
    const CAN_DELETE_RECORD = false;


    protected $_recordListFields = [
        'creationDate', 'name', 'email', 'lastSent',
        'owner', 'registrationDate', 'groups'
    ];

    protected $_recordDetailsFields = [
        'key', 'creationDate', 'owner', 'lastSent',
        'name', 'email', 'message', 'groups',
        'registrationDate', 'user'
    ];

// Record data
    protected function prepareRecordList($query, $mode) {
        $query
            ->populateSelect('groups', 'id', 'name')
            ->importRelationBlock('owner', 'link')
            ->importRelationBlock('user', 'link');
    }

    protected function searchRecordList($query, $search) {
        $query->searchFor($search, [
            'name' => 5,
            'email' => 2,
            'owner|fullName' => 1,
            'user|fullName' => 5
        ]);
    }


// Components
    public function addIndexOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    $this->_getActionRequest('send', [], true),
                    $this->_('Invite user')
                )
                ->setIcon('add')
                ->addAccessLock('axis://user/Invite#add'),

            $this->html->link(
                    $this->_getActionRequest('grant', [], true),
                    $this->_('Grant allowance')
                )
                ->setIcon('edit')
        );
    }

    public function addIndexSubOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    $this->_getActionRequest('export'),
                    $this->_('Export csv')
                )
                ->setIcon('download')
                ->setDisposition('positive'),

            $this->html->link(
                    $this->uri('../settings', true),
                    $this->_('Settings')
                )
                ->setIcon('settings')
                ->setDisposition('operative')
        );
    }

    public function addIndexTransitiveLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    '../invite-requests/',
                    $this->_('Invite requests')
                )
                ->setIcon('key')
                ->setDisposition('transitive')
        );
    }

    public function getRecordOperativeLinks($invite, $mode) {
        return [
            // Resend
            $this->apex->component('InviteLink', $invite, $this->_('Resend invite'))
                ->setAction('resend')
                ->setIcon('refresh')
                ->setDisposition('positive')
                ->isDisabled(!$invite['isActive'] || $invite['registrationDate']),

            // Deactivate
            $this->apex->component('InviteLink', $invite, $this->_('Deactivate invite'))
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
                $context->getRowTag()->addClass('inactive');
            }

            return $this->apex->component('InviteLink', $invite, $this->format->date($invite['creationDate'], 'short'))
                ->setIcon($invite['registrationDate'] ? 'tick' : 'mail')
                ->setDisposition($invite['registrationDate'] ? 'positive' : 'informative');
        });
    }

    public function defineNameField($list) {
        $list->addField('name', function($invite) {
            if($invite['user']) {
                return $this->apex->component('../clients/UserLink', $invite['user']);
            } else {
                return $invite['name'];
            }
        });
    }

    public function defineLastSentField($list, $mode) {
        $list->addField('lastSent', function($invite) {
            return $this->html->timeFromNow($invite['lastSent']);
        });
    }

    public function defineOwnerField($list, $mode) {
        $list->addField('ownerName', $this->_('Sent by'), function($invite) use($mode) {
            $output = $this->apex->component('../clients/UserLink', $invite['owner']);

            if($invite['isFromAdmin']) {
                $output = [
                    $output, ' ',
                    $this->html('sup', '(admin)')
                ];
            }

            return $output;
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
                return $this->apex->component('../groups/GroupLink', $group);
            });
        });
    }

    public function defineUserField($list, $mode) {
        $list->addField('userName', $this->_('Account'), function($invite) {
            return $this->apex->component('../clients/UserLink', $invite['user'])
                ->isNullable(true);
        });
    }
}