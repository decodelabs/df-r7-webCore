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

    protected function createEntries($entryList) {
        $entryList->addEntries(
            $entryList->newLink('~devtools/application/stats', 'File stats')
                ->setId('stats')
                ->setDescription('View file size, code size and spread for this site')
                ->setIcon('stats')
                ->setWeight(10),

            $entryList->newLink('~devtools/application/git/', 'Git packages')
                ->setId('git-packages')
                ->setDescription('View and update details for connected package git repositories')
                ->setIcon('package')
                ->setWeight(20),

            $entryList->newLink('~devtools/application/debug-mode', 'Debug mode')
                ->setId('debug')
                ->setDescription('Force debugging mode for temporary testing sessions on live sites')
                ->setIcon('debug')
                ->setWeight(30)
        );
    }
}