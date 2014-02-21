<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\deactivations\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class IndexHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'remove';

    protected function _getDefaultTitle() {
        return $this->_('User deactivations');
    }

    protected function _addTransitiveLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    '~admin/users/clients/',
                    $this->_('All users')
                )
                ->setIcon('user')
                ->setDisposition('transitive')
        );
    }
}