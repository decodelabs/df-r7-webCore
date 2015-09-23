<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpScaffold extends arch\scaffold\template\AreaMenu {
    
    const DIRECTORY_TITLE = 'Published content';
    const DIRECTORY_ICON = 'content';

    public function generateIndexMenu($entryList) {
        $entryList->addEntries(
            $entryList->newLink('./elements/', 'Elements')
                ->setId('elements')
                ->setDescription('Put together reusable blocks of content to use in your pages')
                ->setIcon('element')
                ->setWeight(20)
        );
    }
}