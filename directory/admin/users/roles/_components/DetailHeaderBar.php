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
    
class DetailHeaderBar extends arch\component\template\HeaderBar {

    protected function _getDefaultTitle() {
        return $this->_('Role: %n%', ['%n%' => $this->_record['name']]);
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            // Edit
            $this->import->component('RoleLink', '~admin/users/roles/', $this->_record, $this->_('Edit role'))
                ->setAction('edit'),

            // Delete
            $this->import->component('RoleLink', '~admin/users/roles/', $this->_record, $this->_('Delete role'))
                ->setAction('delete')
                ->setRedirectTo('~admin/users/roles/')
        );
    }

    protected function _addSubOperativeLinks($menu) {
        if($this->request->isAction('keys')) {
            $menu->addLinks(
                $this->html->link(
                        $this->uri->request('~admin/users/roles/add-key?role='.$this->_record['id'], true),
                        $this->_('Add new key')
                    )
                    ->setIcon('add')
                    ->addAccessLock('axis://user/Key#add')
            );
        }
    }
}