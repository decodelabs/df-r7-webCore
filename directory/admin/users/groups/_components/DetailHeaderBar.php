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
    
class DetailHeaderBar extends arch\component\template\HeaderBar {

    protected function _getDefaultTitle() {
        return $this->_('Group: %n%', ['%n%' => $this->_record['name']]);
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            // Edit
            $this->import->component('GroupLink', '~admin/users/groups/', $this->_record, $this->_('Edit group'))
                ->setAction('edit'),

            // Delete
            $this->import->component('GroupLink', '~admin/users/groups/', $this->_record, $this->_('Delete group'))
                ->setAction('delete')
                ->setRedirectTo('~admin/users/groups/')
        );
    }

    protected function _addSectionLinks($menu) {
        $userCount = $this->_record->users->select()->count();

        $menu->addLinks(
            // Details
            $this->import->component('GroupLink', '~admin/users/groups/', $this->_record, $this->_('Details'), true)
                ->setAction('details')
                ->setIcon('details'),

            // Users
            $this->import->component('GroupLink', '~admin/users/groups/', $this->_record, $this->_('Users'), true)
                ->setAction('users')
                ->setIcon('user')
                ->setNote($this->format->counterNote($userCount))
        );
    }
}