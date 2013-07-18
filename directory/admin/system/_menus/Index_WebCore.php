<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
    
class Index_WebCore extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
        $entryList->addEntries(
            $entryList->newLink('~admin/system/error-logs/', 'Error logs')
                ->setId('errorLogs')
                ->setDescription('View logs generated for missing content and system errors')
                ->setIcon('log')
                ->setWeight(10)
        );
    }
}