<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\cache\_menus;

use df\arch;

class Index_WebCore extends arch\navigation\menu\Base
{
    protected function createEntries($entryList)
    {
        $entryList->addEntries(
            $entryList->newLink('~devtools/cache/axis', 'Axis schemas')
                ->setId('axis')
                ->setIcon('database')
                ->setWeight(10),
            $entryList->newLink('~devtools/cache/hooks', 'Event hooks')
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
