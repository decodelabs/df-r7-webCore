<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\navigation\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
    
class Index_WebCore extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
    	$entryList->addEntries(
    		$entryList->newLink('~admin/navigation/directory/', 'System menus')
    			->setId('system')
    			->setDescription('View and modify pre-defined system menus')
    			->setIcon('menu')
    			->setWeight(10)
		);
    }
}