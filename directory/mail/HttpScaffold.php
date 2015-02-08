<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpScaffold extends arch\scaffold\template\AreaMenu {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const DIRECTORY_TITLE = 'Mail';
    const DIRECTORY_ICON = 'mail';
    const HEADER_BAR = false;

    public function generateIndexMenu($entryList) {
        $captureCount = $this->data->mail->capture->countAll();
        $journalCount = $this->data->mail->journal->countAll();

        $entryList->addEntries(
            $entryList->newLink('~mail/capture/', 'Development mailbox')
                ->setId('capture')
                ->setDescription('When testing, all outgoing emails are diverted to a local mailbox to avoid unwanted spam - view them here')
                ->setIcon('mail')
                ->setNote($this->format->counterNote($captureCount))
                ->setWeight(10),

            $entryList->newLink('~mail/journal/', 'Send logs')
                ->setId('journal')
                ->setDescription('Get an overview of what emails have been sent recently')
                ->setIcon('log')
                ->setNote($this->format->counterNote($journalCount))
                ->setWeight(20),

            $entryList->newLink('~mail/templates/', 'Template visualisation')
                ->setId('templates')
                ->setDescription('View component based mail templates with pre-set test data')
                ->setIcon('theme')
                ->setWeight(30),

            $entryList->newLink('~mail/test', 'Test email capabilities')
                ->setId('test')
                ->setDescription('Try sending test emails from any of the available transports')
                ->setIcon('test')
                ->setWeight(40)
        );
    }
}