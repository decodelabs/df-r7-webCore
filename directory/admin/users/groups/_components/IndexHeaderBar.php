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
    
class IndexHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'group';

    protected function _getDefaultTitle() {
        return $this->_('Groups');
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~admin/users/groups/add', true),
                    $this->_('Add new group')
                )
                ->setIcon('add')
                ->addAccessLock('axis://user/Group#add')
        );
    }

    protected function _addTransitiveLinks($menu) {
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
}