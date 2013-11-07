<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\clients\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class DetailHeaderBar extends arch\component\template\HeaderBar {

    protected function _getDefaultTitle() {
        return $this->_('User: %n%', ['%n%' => $this->_record['fullName']]);
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            // Edit
            $this->import->component('UserLink', '~admin/users/clients/', $this->_record, $this->_('Edit user'))
                ->setAction('edit'),

            // Delete
            $this->import->component('UserLink', '~admin/users/clients/', $this->_record, $this->_('Delete user'))
                ->setAction('delete')
                ->setRedirectTo('~admin/users/clients/')
        );
    }

    protected function _addSubOperativeLinks($menu) {
        if($this->request->isAction('details') && $this->_record->hasLocalAuth()) {
            $menu->addLinks(
                // Change password
                $this->html->link(
                        $this->uri->request('~admin/users/clients/change-password?user='.$this->_record['id'], true),
                        $this->_('Change password')
                    )
                    ->setIcon('edit')
                    ->setDisposition('operative')
            );
        }
    }

    protected function _addSectionLinks($menu) {
        $menu->addLinks('directory://~admin/users/clients/SectionLinks');
    }
}