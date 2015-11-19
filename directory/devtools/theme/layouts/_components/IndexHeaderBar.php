<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\theme\layouts\_components;

use df;
use df\core;
use df\apex;
use df\arch;

class IndexHeaderBar extends arch\component\HeaderBar {

    protected $_icon = 'layout';

    protected function _getDefaultTitle() {
        return $this->_('Layouts');
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri('~devtools/theme/layouts/add', true),
                    $this->_('Add new layout')
                )
                ->setIcon('add')
        );
    }
}