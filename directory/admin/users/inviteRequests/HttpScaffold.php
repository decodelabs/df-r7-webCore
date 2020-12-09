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

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    const TITLE = 'Invite requests';
    const ICON = 'key';
    const ADAPTER = 'axis://user/InviteRequest';
    const KEY_NAME = 'request';
    const ITEM_NAME = 'Invite request';

    const CAN_ADD = false;
    const CAN_EDIT = false;

    const LIST_FIELDS = [
        'name', 'email', 'companyName', 'companyPosition',
        'creationDate', 'isActive'
    ];

    const DETAILS_FIELDS = [
        'name', 'email', 'companyName', 'companyPosition',
        'invite', 'user', 'creationDate', 'isActive', 'message'
    ];


    // Components
    public function generateIndexOperativeLinks(): iterable
    {
        yield 'export' => $this->html->link(
                $this->getNodeUri('export'),
                $this->_('Export to csv')
            )
            ->setIcon('download')
            ->setDisposition('positive');
    }

    public function generateIndexTransitiveLinks(): iterable
    {
        yield 'invites' => $this->html->link(
                '../invites/',
                $this->_('Invites')
            )
            ->setIcon('mail')
            ->setDisposition('transitive');
    }

    public function getRecordOperativeLinks($request, $mode)
    {
        // Respond
        yield $this->apex->component('RequestLink', $request, $this->_('Respond'))
            ->setNode('respond')
            ->setIcon('mail')
            ->setDisposition('operative')
            ->isDisabled(!$request['isActive']);

        yield parent::getRecordOperativeLinks($request, $mode);
    }


    // Fields
    public function defineCompanyNameField($list, $mode)
    {
        $list->addField('companyName', $this->_('Company'));
    }

    public function defineCompanyPositionField($list, $mode)
    {
        $list->addField('companyPosition', $this->_('Position'));
    }

    public function defineInviteField($list, $mode)
    {
        $list->addField('invite', function ($request) {
            return $this->apex->component('../invites/InviteLink', $request['invite'])
                ->isNullable(true);
        });
    }

    public function defineUserField($list, $mode)
    {
        $list->addField('user', function ($request) {
            return $this->apex->component('../clients/UserLink', $request['user'])
                ->isNullable(true);
        });
    }

    public function defineMessageField($list, $mode)
    {
        $list->addField('message', function ($request) {
            return $this->html->plainText($request['message']);
        });
    }

    public function defineIsActiveField($list, $mode)
    {
        $list->addField('isActive', $this->_('Status'), function ($request, $context) use ($mode) {
            if ($mode == 'list' && !$request['isActive']) {
                $context->getRowTag()->addClass('inactive');
            }

            if (!$request['isActive'] && (isset($request['invite']) || isset($request['user']))) {
                return $this->html->icon('accept', $mode != 'list' ? $this->_('Accepted') : null)
                    ->addClass('positive');
            } elseif ($request['isActive']) {
                return $this->html->icon('priority-critical', $mode != 'list' ? $this->_('Awaiting response') : null)
                    ->addClass('warning');
            } else {
                return $this->html->icon('deny', $mode != 'list' ? $this->_('Denied') : null)
                    ->addClass('negative');
            }
        });
    }
}
