<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\groups\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class GroupList extends arch\component\template\CollectionList {

    protected $_fields = [
        'name' => true,
        'users' => true,
        'roles' => true,
        'actions' => true
    ];


// Name
    public function addNameField($list) {
        $list->addField('name', function($group) {
            return $this->view->import->component('GroupLink', '~admin/users/groups/', $group)
                ->setRedirectFrom($this->_urlRedirect);
        });
    }

// Actions
    public function addActionsField($list) {
        $list->addField('actions', function($group) {
            return [
                // Edit
                $this->view->import->component('GroupLink', '~admin/users/groups/', $group, $this->_('Edit'))
                    ->setAction('edit')
                    ->addAccessLock('axis://user/Group#edit'),

                // Delete
                $this->view->import->component('GroupLink', '~admin/users/groups/', $group, $this->_('Delete'))
                    ->setAction('delete')
                    ->addAccessLock('axis://user/Group#delete'),
            ];
        });
    }
}