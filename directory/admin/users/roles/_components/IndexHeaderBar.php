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
    
class IndexHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'role';

    protected function _getDefaultTitle() {
        return $this->_('Roles');
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->to('~admin/users/roles/add', true),
                    $this->_('Add new role')
                )
                ->setIcon('add')
                ->addAccessLock('axis://user/Role#add')
        );
    }

    protected function _addTransitiveLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    '~admin/users/groups/',
                    $this->_('View groups')
                )
                ->setIcon('group')
                ->setDisposition('transitive')
                ->addAccessLock('axis://user/Group')
        );  
    }
}