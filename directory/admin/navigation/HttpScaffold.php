<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\navigation;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpScaffold extends arch\scaffold\template\AreaMenu {
    
    const DIRECTORY_TITLE = 'Navigation';
    const DIRECTORY_ICON = 'link';

    public function generateIndexMenu($entryList) {
        $entryList->addEntries(
            $entryList->newLink('./directory/', 'System menus')
                ->setId('system')
                ->setDescription('View and modify pre-defined system menus')
                ->setIcon('menu')
                ->setWeight(10)
        );
    }

    public function addIndexOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri('./refresh', true),
                    $this->_('Refresh menu list')
                )
                ->setIcon('refresh')
        );
    }
}