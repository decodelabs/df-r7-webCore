<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\application\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
    
class Index_WebCore extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
        $entryList->addEntries(
            $entryList->newLink('~devtools/application/stats', 'File stats')
                ->setId('stats')
                ->setDescription('View file size, code size and spread for this site')
                ->setIcon('stats')
                ->setWeight(10),

            $entryList->newLink('~devtools/application/packages/', 'Packages')
                ->setId('packages')
                ->setDescription('Select, install and update packages for features of your site')
                ->setIcon('package')
                ->setWeight(20),

            $entryList->newLink('~devtools/application/compile', 'Compile production version')
                ->setId('compile')
                ->setDescription('Production sites should run from a compiled production version. Build an up to date version here')
                ->setIcon('module')
                ->setWeight(30)
        );
    }
}