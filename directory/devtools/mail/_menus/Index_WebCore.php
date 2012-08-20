<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\mail\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
    
class Index_WebCore extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
        $entryList->addEntries(
            $entryList->newLink('~devtools/mail/dev/', 'Development inbox')
                ->setId('devMail')
    			->setDescription('When in development, all outgoing emails are diverted to a local inbox to avoid spam - view them here')
    			->setIcon('mail')
    			->setWeight(10),

			$entryList->newLink('~devtools/mail/test', 'Test email capabilities')
				->setId('test')
				->setDescription('Try sending test emails from any of the available transports')
				->setIcon('test')
				->setWeight(20)
		);
    }
}