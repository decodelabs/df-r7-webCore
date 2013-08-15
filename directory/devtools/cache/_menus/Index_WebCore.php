<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\cache\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
    
class Index_WebCore extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
        $entryList->addEntries(
            $entryList->newLink('~devtools/cache/axis', 'Axis schemas')
                ->setId('axis')
                ->setIcon('database')
                ->setWeight(10),

            $entryList->newLink('~devtools/cache/menu', 'Navigation menu')
                ->setId('menu')
                ->setIcon('menu')
                ->setWeight(20),

            $entryList->newLink('~devtools/cache/hooks', 'Policy hooks')
                ->setId('hooks')
                ->setIcon('hook')
                ->setWeight(30),

            $entryList->newLink('~devtools/cache/session', 'Session store')
                ->setId('session')
                ->setIcon('user')
                ->setWeight(40),

            $entryList->newLink('~devtools/cache/raster', 'Raster image transformations')
                ->setId('raster')
                ->setIcon('image')
                ->setWeight(50)
        );
    }
}