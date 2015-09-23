<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\media\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
    
class Index_Media extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
        $entryList->addEntries(
            $entryList->newLink('~devtools/media/transfer', 'Transfer library')
                ->setId('transfer')
                ->setDescription('Transfer your media library to another file store')
                ->setIcon('upload')
                ->setWeight(10)
        );
    }
}