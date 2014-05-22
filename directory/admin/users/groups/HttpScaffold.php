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

    protected $_sections = [
        'details',
        'users' => [
            'icon' => 'user'
        ]
    ];

    protected $_recordListFields = [
        'name' => true,
        'roles' => true,
        'users' => true,
        'actions' => true
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
            'users' => $record->users->select()->count()
        ];
    }

    public function deleteRecord(opal\record\IRecord $group, array $flags=[]) {
        $group->delete();
        $this->user->instigateGlobalKeyringRegeneration();
        return $this;
    }

// Components
    public function addIndexHeaderBarTransitiveLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    '~admin/users/roles/',
                    $this->_('View roles')
                )
                ->setIcon('role')
                ->setDisposition('transitive')
                ->addAccessLock('axis://user/Role')
        );
    }


// Secions
    public function renderDetailsSectionBody($group) {
        $roleList = $group->roles->fetch()
            ->countRelation('groups')
            ->countRelation('keys')
            ->orderBy('priority');

        return [
            $this->html->element('h3', $this->_('Roles')),

            $this->import->component('RoleList', '~admin/users/roles/', [
                'actions' => false
            ], $roleList)
        ];
    }

    public function renderUsersSectionBody($group) {
        $userList = $group->users->select()
            ->countRelation('groups')
            ->paginateWith($this->request->query);

        return $this->import->component('UserList', '~admin/users/clients/')
            ->setCollection($userList);
    }

// Fields
    public function defineRolesField($list, $mode) {
        if($mode == 'list') {
            return false;
        }

        $list->addField('roles', function($group) {
            return $this->html->bulletList($group->roles->select()->orderBy('name'), function($role) {
                return $this->import->component('RoleLink', '~admin/users/roles/', $role)
                    ->setDisposition('transitive');
            });
        });
    }
}