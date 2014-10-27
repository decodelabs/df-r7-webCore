<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\navigation\directory\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class DetailHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'menu';

    protected function _getDefaultTitle() {
        return $this->_('Menu: %n%', ['%n%' => $this->_record->getDisplayName()]);
    }

    protected function _addOperativeLinks($menu) {
        $menuId = $this->_record->getId()->path->toString();

        $menu->addLinks(
            $this->html->link(
                    $this->uri('~admin/navigation/directory/edit?menu='.$menuId, true),
                    $this->_('Edit menu')
                )
                ->setIcon('edit')
        );
    }
}