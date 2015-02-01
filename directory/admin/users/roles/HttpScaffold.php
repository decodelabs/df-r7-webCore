<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\roles;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;
use df\user;

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DIRECTORY_TITLE = 'Roles';
    const DIRECTORY_ICON = 'role';
    const RECORD_ADAPTER = 'axis://user/Role';


    protected $_recordListFields = [
        'name' => true,
        'bindState' => true,
        'minRequiredState' => true,
        'priority' => true,
        'groups' => true,
        'keys' => true,
        'actions' => true
    ];

    protected $_recordDetailsFields = [
        'name' => true,
        'bindState' => true,
        'minRequiredState' => true,
        'priority' => true,
        'groups' => true
    ];

// Record data
    protected function _prepareRecordListQuery(opal\query\ISelectQuery $query, $mode) {
        $query
            ->countRelation('groups')
            ->countRelation('keys');
    }


    public function deleteRecord(opal\record\IRecord $role, array $flags=[]) {
        $role->delete();
        $this->user->instigateGlobalKeyringRegeneration();
        return $this;
    }

// Components
    public function addIndexTransitiveLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link('../groups/', $this->_('View groups'))
                ->setIcon('group')
                ->setDisposition('transitive')
                ->addAccessLock('axis://user/Group')
        );
    }

    public function addDetailsSectionSubOperativeLinks($menu, $bar) {
        $menu->addLinks(
            // Add key
            $this->html->link(
                    $this->uri('./add-key?role='.$this->_record['id'], true),
                    $this->_('Add new key')
                )
                ->setIcon('add')
                ->addAccessLock('axis://user/Key#add')
        );
    }


// Sections
    public function renderDetailsSectionBody($role) {
        $keyList = $role->keys->fetch()
            ->orderBy('domain');

        return [
            parent::renderDetailsSectionBody($role),
            $this->apex->template('Details.html', [
                'keyList' => $keyList
            ])
        ];
    }


// Fields
    public function defineBindStateField($list) {
        $list->addField('bindState', $this->_('Bind state'), function($role) {
            if($role['bindState'] !== null) {
                return user\Client::stateIdToName($role['bindState']);
            }
        });
    }

    public function defineMinRequiredStateField($list) {
        $list->addField('minRequiredState', $this->_('Min. required state'), function($role) {
            if($role['minRequiredState'] !== null) {
                return user\Client::stateIdToName($role['minRequiredState']);
            }
        });
    }

    public function definePriorityField($list, $mode) {
        $list->addField('priority');
    }

    public function defineGroupsField($list, $mode) {
        if($mode == 'list') {
            return false;
        }

        $list->addField('groups', function($role) {
            return $this->html->bulletList($role->groups->fetch()->orderBy('name'), function($group) {
                return $this->apex->component('../groups/GroupLink', $group);
            });
        });
    }
}