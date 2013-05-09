<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\_components;

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
            $this->html->link(
                    $this->uri->request('~admin/users/edit?user='.$this->_record['id'], true),
                    $this->_('Edit user')
                )
                ->setIcon('edit')
                ->addAccessLock($this->_record->getActionLock('edit')),

            // Delete
            $this->html->link(
                    $this->uri->request(
                        '~admin/users/delete?user='.$this->_record['id'], true,
                        '~admin/users/'
                    ),
                    $this->_('Delete user')
                )
                ->setIcon('delete')
                ->addAccessLock($this->_record->getActionLock('delete'))
        );
    }

    protected function _addSubOperativeLinks($menu) {
        if($this->request->isAction('details') && $this->_record->hasLocalAuth()) {
            $menu->addLinks(
                // Change password
                $this->html->link(
                        $this->uri->request('~admin/users/change-password?user='.$this->_record['id'], true),
                        $this->_('Change password')
                    )
                    ->setIcon('edit')
                    ->setDisposition('operative')
            );
        }
    }

    protected function _addSectionLinks($menu) {
        $authenticationCount = $this->_record->authDomains->select()->count();

        $menu->addLinks(
            // Details
            $this->html->link(
                    '~admin/users/details?user='.$this->_record['id'],
                    $this->_('Details'),
                    true
                )
                ->setIcon('details')
                ->setDisposition('informative'),


            // Authentication
            $this->html->link(
                    '~admin/users/authentication?user='.$this->_record['id'],
                    $this->_('Authentication'),
                    true
                )
                ->setNote($this->format->counterNote($authenticationCount))
                ->setIcon('user')
                ->setDisposition('informative')
        );
    }
}