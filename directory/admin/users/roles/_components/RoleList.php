<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\roles\_components;

use df;
use df\core;
use df\apex;
use df\arch;
use df\user;
    
class RoleList extends arch\component\template\CollectionList {

    protected $_fields = [
        'name' => true,
        'bindState' => true,
        'minRequiredState' => true,
        'priority' => true,
        'groups' => true,
        'keys' => true,
        'actions' => true
    ];


// Name
    public function addNameField($list) {
        $list->addField('name', function($role) {
            return $this->import->component('RoleLink', '~admin/users/roles/', $role)
                ->setRedirectFrom($this->_urlRedirect);
        });
    }


// Bind state
    public function addBindStateField($list) {
        $list->addField('bindState', $this->_('Bind state'), function($role) {
            if($role['bindState'] !== null) {
                return user\Client::stateIdToName($role['bindState']);
            }
        });
    }


// Min required state
    public function addMinRequiredStateField($list) {
        $list->addField('minRequiredState', $this->_('Minimum required state'), function($role) {
            if($role['minRequiredState'] !== null) {
                return user\Client::stateIdToName($role['minRequiredState']);
            }
        });
    }


// Actions
    public function addActionsField($list) {
        $list->addField('actions', function($role) {
            return [
                // Edit
                $this->import->component('RoleLink', '~admin/users/roles/', $role, $this->_('Edit'))
                    ->setAction('edit'),

                // Delete
                $this->import->component('RoleLink', '~admin/users/roles/', $role, $this->_('Delete'))
                    ->setAction('delete')
            ];
        });
    }
}