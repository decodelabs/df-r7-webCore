<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\groups;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DIRECTORY_TITLE = 'Groups';
    const DIRECTORY_ICON = 'group';
    const RECORD_ADAPTER = 'axis://user/Group';
    const DEFAULT_RECORD_ACTION = 'users';
    const SELECTOR_TYPE = 'list';

    protected $_sections = [
        'details',
        'users' => 'user'
    ];

    protected $_recordListFields = [
        'name', 'signifier', 'roles', 'users'
    ];

// Record data
    protected function _prepareRecordListQuery(opal\query\ISelectQuery $query, $mode) {
        $query
            ->countRelation('users')
            ->countRelation('roles');
    }

    protected function _fetchSectionItemCounts() {
        $record = $this->getRecord();

        return [
            'users' => $record->users->countAll()
        ];
    }

    public function deleteRecord(opal\record\IRecord $group, array $flags=[]) {
        $group->delete();
        $this->user->instigateGlobalKeyringRegeneration();
        return $this;
    }

// Components
    public function addIndexTransitiveLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link('../roles/', $this->_('View roles'))
                ->setIcon('role')
                ->setDisposition('transitive')
                ->addAccessLock('axis://user/Role')
        );
    }


// Secions
    public function renderDetailsSectionBody($group) {
        return $this->apex->scaffold('../roles/')
            ->renderRecordList(
                $group->roles->select(),
                ['actions' => false]
            );
    }

    public function renderUsersSectionBody($group) {
        return $this->apex->scaffold('../clients/')
            ->renderRecordList($group->users->select());
    }

// Fields
    public function defineSignifierField($list, $mode) {
        $list->addField('signifier', function($group) {
            if(!$group['signifier']) return null;
            return $this->html('samp', $group['signifier']);
        });
    }

    public function defineRolesField($list, $mode) {
        if($mode == 'list') {
            return false;
        }

        $list->addField('roles', function($group) {
            return $this->html->bulletList($group->roles->select()->orderBy('name'), function($role) {
                return $this->apex->component('../roles/RoleLink', $role);
            });
        });
    }
}