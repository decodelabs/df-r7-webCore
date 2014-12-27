<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\inviteRequests;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DIRECTORY_TITLE = 'Invite requests';
    const DIRECTORY_ICON = 'key';
    const RECORD_ADAPTER = 'axis://user/InviteRequest';
    const RECORD_KEY_NAME = 'request';
    const RECORD_ITEM_NAME = 'Invite request';

    const CAN_ADD_RECORD = false;
    const CAN_EDIT_RECORD = false;

    protected $_recordListFields = [
        'name', 'email', 'companyName', 'companyPosition',
        'creationDate', 'isActive', 'actions'
    ];

    protected $_recordDetailsFields = [
        'name', 'email', 'companyName', 'companyPosition',
        'invite', 'creationDate', 'isActive', 'message'
    ];


// Components
    public function addIndexOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    $this->_getActionRequest('export'),
                    $this->_('Export to csv')
                )
                ->setIcon('download')
                ->setDisposition('positive')
        );
    }

    public function addIndexTransitiveLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    '~admin/users/invites/',
                    $this->_('Invites')
                )
                ->setIcon('mail')
                ->setDisposition('transitive')
        );
    }

    public function getRecordOperativeLinks($request, $mode) {
        return array_merge(
            [
                // Respond
                $this->apex->component('~admin/users/invite-requests/RequestLink', $request, $this->_('Respond'))
                    ->setAction('respond')
                    ->setIcon('mail')
                    ->setDisposition('operative')
                    ->isDisabled(!$request['isActive'])
            ],
            parent::getRecordOperativeLinks($request, $mode)
        );
    }


// Fields
    public function defineCompanyNameField($list, $mode) {
        $list->addField('companyName', $this->_('Company'));
    }

    public function defineCompanyPositionField($list, $mode) {
        $list->addField('companyPosition', $this->_('Position'));
    }

    public function defineInviteField($list, $mode) {
        $list->addField('invite', function($request) {
            return $this->apex->component('~admin/users/invites/InviteLink', $request['invite'])
                ->isNullable(true);
        });
    }

    public function defineMessageField($list, $mode) {
        $list->addField('message', function($request) {
            return $this->html->plainText($request['message']);
        });
    }

    public function defineIsActiveField($list, $mode) {
        $list->addField('isActive', $this->_('Status'), function($request, $context) use($mode) {
            if($mode == 'list' && !$request['isActive']) {
                $context->getRowTag()->addClass('inactive');
            }

            if(isset($request['invite'])) {
                return $this->html->icon('accept', $mode != 'list' ? $this->_('Accepted') : null)
                    ->addClass('positive');
            } else if($request['isActive']) {
                return $this->html->icon('priority-critical', $mode != 'list' ? $this->_('Awaiting response') : null)
                    ->addClass('warning');
            } else {
                return $this->html->icon('deny', $mode != 'list' ? $this->_('Denied') : null)
                    ->addClass('negative');
            }
        });
    }
}