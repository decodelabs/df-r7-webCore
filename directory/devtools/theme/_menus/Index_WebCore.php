<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\theme\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
    
class Index_WebCore extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
        $entryList->addEntries(
            $entryList->newLink('~devtools/theme/layouts/', 'Layout setup')
                ->setId('layouts')
    			->setDescription('Configure layout definitions for dynamic view generation')
    			->setIcon('layout')
    			->setWeight(10)
		);
    }
}