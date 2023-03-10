<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\invites;

use DecodeLabs\Metamorph;

use DecodeLabs\Tagged as Html;
use df\arch;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const TITLE = 'Invites';
    public const ICON = 'mail';
    public const ADAPTER = 'axis://user/Invite';
    public const NAME_FIELD = 'email';

    public const CAN_ADD = false;
    public const CAN_EDIT = false;


    public const LIST_FIELDS = [
        'creationDate', 'name', 'email', 'lastSent',
        'owner', 'registrationDate', 'groups'
    ];

    public const DETAILS_FIELDS = [
        'key', 'link', 'creationDate', 'owner', 'lastSent',
        'name', 'email', 'message', 'groups',
        'registrationDate', 'user'
    ];

    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query
            ->populateSelect('groups', 'id', 'name')
            ->importRelationBlock('owner', 'link')
            ->importRelationBlock('user', 'link');
    }

    protected function searchRecordList($query, $search)
    {
        $query->searchFor($search, [
            'name' => 5,
            'email' => 2,
            'owner|fullName' => 1,
            'user|fullName' => 5
        ]);
    }

    public function isRecordDeleteable($record): bool
    {
        return
            !$record['isActive'] &&
            !$record['registrationDate'];
    }


    // Components
    public function generateRecordOperativeLinks(array $invite): iterable
    {
        // Resend
        yield 'resend' => $this->apex->component('InviteLink', $invite, $this->_('Resend invite'))
            ->setNode('resend')
            ->setIcon('refresh')
            ->setDisposition('positive')
            ->isDisabled(!$invite['isActive'] || $invite['registrationDate']);

        // Deactivate
        yield 'deactivate' => $this->apex->component('InviteLink', $invite, $this->_('Deactivate invite'))
            ->setNode('deactivate')
            ->setIcon('remove')
            ->setDisposition('negative')
            ->isDisabled(!$invite['isActive']);

        yield from parent::generateRecordOperativeLinks($invite);
    }

    public function generateIndexOperativeLinks(): iterable
    {
        yield 'send' => $this->html->link(
            $this->getNodeUri('send', [], true),
            $this->_('Invite user')
        )
            ->setIcon('add')
            ->addAccessLock('axis://user/Invite#add');
    }

    public function generateIndexSubOperativeLinks(): iterable
    {
        yield 'export' => $this->html->link(
            $this->getNodeUri('export'),
            $this->_('Export csv')
        )
            ->setIcon('download')
            ->setDisposition('positive');

        yield 'settings' => $this->html->link(
            $this->uri('../settings', true),
            $this->_('Settings')
        )
            ->setIcon('settings')
            ->setDisposition('operative');
    }

    public function generateIndexTransitiveLinks(): iterable
    {
        yield 'inviteRequests' => $this->html->link(
            '../invite-requests/',
            $this->_('Invite requests')
        )
            ->setIcon('key')
            ->setDisposition('transitive');
    }




    // Sections
    public function renderDetailsSectionBody($invite)
    {
        $output = parent::renderDetailsSectionBody($invite);

        if (!$invite['isActive']) {
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
    public function defineCreationDateField($list, $mode)
    {
        if ($mode != 'list') {
            return false;
        }

        $list->addField('creationDate', $this->_('Created'), function ($invite, $context) {
            if (!$invite['isActive'] && !$invite['registrationDate']) {
                $context->getRowTag()->addClass('inactive');
            }

            return $this->apex->component('InviteLink', $invite, Html::$time->shortDate($invite['creationDate']))
                ->setIcon($invite['registrationDate'] ? 'tick' : 'mail')
                ->setDisposition($invite['registrationDate'] ? 'positive' : 'informative');
        });
    }

    public function defineLinkField($list, $mode)
    {
        $list->addField('link', function ($invite) {
            return $this->html->link('account/register?invite=' . $invite['key']);
        });
    }

    public function defineNameField($list, $mode)
    {
        $list->addField('name', function ($invite) {
            if ($invite['user']) {
                return $this->apex->component('../clients/UserLink', $invite['user']);
            } else {
                return $invite['name'];
            }
        });
    }

    public function defineLastSentField($list, $mode)
    {
        $list->addField('lastSent', function ($invite) {
            return Html::$time->since($invite['lastSent']);
        });
    }

    public function defineOwnerField($list, $mode)
    {
        $list->addField('ownerName', $this->_('Sent by'), function ($invite) {
            $output = $this->apex->component('../clients/UserLink', $invite['owner']);

            if ($invite['isFromAdmin']) {
                $output = [
                    $output, ' ',
                    Html::{'sup'}('(admin)')
                ];
            }

            return $output;
        });
    }

    public function defineRegistrationDateField($list)
    {
        $list->addField('registrationDate', $this->_('Registered'), function ($invite) {
            return Html::$time->date($invite['registrationDate']);
        });
    }

    public function defineMessageField($list)
    {
        $list->addField('message', function ($invite) {
            return Metamorph::idiom($invite['message']);
        });
    }

    public function defineGroupsField($list, $mode)
    {
        $list->addField('groups', function ($invite) use ($mode) {
            if ($mode == 'list') {
                $groups = $invite['groups'];
            } else {
                $groups = $invite->groups->select();
            }

            return Html::uList($groups, function ($group) {
                if (!$group['id']) {
                    $group = null;
                }

                return $this->apex->component('../groups/GroupLink', $group);
            });
        });
    }

    public function defineUserField($list, $mode)
    {
        $list->addField('userName', $this->_('Account'), function ($invite) {
            return $this->apex->component('../clients/UserLink', $invite['user'])
                ->isNullable(true);
        });
    }
}
