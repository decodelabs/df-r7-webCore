<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\roles;

use DecodeLabs\Tagged as Html;
use df\arch;

use df\opal;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const TITLE = 'Roles';
    public const ICON = 'role';
    public const ADAPTER = 'axis://user/Role';
    public const IS_SHARED = true;

    public const LIST_FIELDS = [
        'name', 'signifier', 'priority', 'groups', 'keys'
    ];

    public const DETAILS_FIELDS = [
        'name', 'signifier', 'priority', 'groups'
    ];

    public const CONFIRM_DELETE = true;

    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query
            ->countRelation('groups')
            ->countRelation('keys');
    }


    public function deleteRecord(opal\record\IRecord $role, array $flags = [])
    {
        $role->delete();
        $this->user->instigateGlobalKeyringRegeneration();
        return $this;
    }


    // Sections
    public function renderDetailsSectionBody($role)
    {
        $keyList = $role->keys->fetch()
            ->orderBy('domain ASC', 'pattern ASC');

        return [
            parent::renderDetailsSectionBody($role),
            $this->apex->template('Details.html', [
                'keyList' => $keyList
            ])
        ];
    }


    // Components
    public function generateIndexTransitiveLinks(): iterable
    {
        yield 'groups' => $this->html->link('../groups/', $this->_('View groups'))
            ->setIcon('group')
            ->setDisposition('transitive')
            ->addAccessLock('axis://user/Group');
    }

    public function generateDetailsSectionSubOperativeLinks(): iterable
    {
        // Add key
        yield 'addKey' => $this->html->link(
            $this->uri('./add-key?role=' . $this->getRecordId(), true),
            $this->_('Add new key')
        )
            ->setIcon('add')
            ->addAccessLock('axis://user/Key#add');
    }


    // Fields
    public function definePriorityField($list, $mode)
    {
        $list->addField('priority');
    }

    public function defineSignifierField($list, $mode)
    {
        $list->addField('signifier', function ($role) {
            if (!$role['signifier']) {
                return null;
            }
            return Html::{'samp'}($role['signifier']);
        });
    }

    public function defineGroupsField($list, $mode)
    {
        if ($mode == 'list') {
            return false;
        }

        $list->addField('groups', function ($role) {
            return Html::uList($role->groups->fetch()->orderBy('name'), function ($group) {
                return $this->apex->component('../groups/GroupLink', $group);
            });
        });
    }
}
