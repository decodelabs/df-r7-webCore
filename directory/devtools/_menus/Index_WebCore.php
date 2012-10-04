<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
    
class Index_WebCore extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
        $entryList->addEntries(
            $entryList->newLink('~devtools/users/', 'User setup')
                ->setId('users')
                ->setDescription('Configure root user, authentication adapters and session settings')
                ->setIcon('user')
                ->setWeight(10),

            $entryList->newLink('~devtools/mail/', 'Mail utilities')
                ->setId('mail')
                ->setDescription('View development mail and test active mail transports')
                ->setIcon('mail')
                ->setWeight(20),

            $entryList->newLink('~devtools/theme/', 'Theme configuration')
                ->setId('theme')
                ->setDescription('Change settings and define layouts for available site themes')
                ->setIcon('theme')
                ->setWeight(30),

            $entryList->newLink('~devtools/stats', 'Application file stats')
                ->setId('stats')
                ->setDescription('View file size, code size and spread for this site')
                ->setIcon('stats')
                ->setWeight(50)
        );

        if($this->_context->arch->actionExists('~devtools/regenerate-test-db')) {
            $entryList->addEntry(
                $entryList->newLink('~devtools/regenerate-test-db', 'Regenerate test DB')
                    ->setId('regenerate-db')
                    ->setDescription('Delete and regenerate the entire database with test data')
                    ->setIcon('info')
                    ->setWeight(45)
            );
        }
    }
}