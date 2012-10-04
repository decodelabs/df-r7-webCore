<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\users\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
    
class Index_WebCore extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
        if(!$this->_context->data->getModel('user')->client->select()->count()) {
            $entryList->addEntry(
                $entryList->newLink('~devtools/users/setup-user', 'Add root user')
                    ->setId('setup-root')
                    ->setDescription('Before you can really do anything with your site, you will need a root user account')
                    ->setIcon('add')
                    ->setWeight(5)
            );
        }
    }
}