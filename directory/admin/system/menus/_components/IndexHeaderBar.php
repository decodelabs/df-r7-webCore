<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\menus\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class IndexHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'menu';

    protected function _getDefaultTitle() {
        return $this->_('System menus');
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri('./refresh', true),
                    $this->_('Refresh menu list')
                )
                ->setIcon('refresh')
        );
    }
}