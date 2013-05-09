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
    
class IndexHeaderBar extends arch\component\template\HeaderBar {

    protected function _getDefaultTitle() {
        return $this->_('Users');
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~admin/users/add', true),
                    $this->_('Add new user')
                )
                ->setIcon('add')
                ->addAccessLock('axis://user/Client#add')
        );
    }

    protected function _addTransitiveLinks($menu) {
        $menu->addLinks(
            $this->html->link('~admin/users/groups/', $this->_('View groups'))
                ->setIcon('group')
                ->setDisposition('transitive'),

            $this->html->link('~admin/users/roles/', $this->_('View roles'))
                ->setIcon('role')
                ->setDisposition('transitive')
        );
    }
}