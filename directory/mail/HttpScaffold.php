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

class HttpScaffold extends arch\scaffold\AreaMenu {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const TITLE = 'Mail';
    const ICON = 'mail';
    const HEADER_BAR = false;

    public function generateIndexMenu($entryList) {
        $captureCount = $this->data->mail->capture->countAll();
        $journalCount = $this->data->mail->journal->countAll();

        $entryList->addEntries(
            $entryList->newLink('~mail/lists/', 'Mailing lists')
                ->setId('lists')
                ->setDescription('Configure mailing lists')
                ->setIcon('list')
                ->setWeight(10),

            $entryList->newLink('~mail/capture/', 'Development mailbox')
                ->setId('capture')
                ->setDescription('When testing, all outgoing emails are diverted to a local mailbox to avoid unwanted spam - view them here')
                ->setIcon('mail')
                ->setNote($this->format->counterNote($captureCount))
                ->setWeight(20),

            $entryList->newLink('~mail/journal/', 'Send logs')
                ->setId('journal')
                ->setDescription('Get an overview of what emails have been sent recently')
                ->setIcon('log')
                ->setNote($this->format->counterNote($journalCount))
                ->setWeight(30),

            $entryList->newLink('~mail/previews/', 'Mail previews')
                ->setId('previews')
                ->setDescription('View prepared mail previews with pre-set test data')
                ->setIcon('theme')
                ->setWeight(40),

            $entryList->newLink('~mail/components/', 'Mail components')
                ->setId('components')
                ->setDescription('View component based mail templates with pre-set test data')
                ->setIcon('theme')
                ->setWeight(41),

            $entryList->newLink('~mail/test', 'Test email capabilities')
                ->setId('test')
                ->setDescription('Try sending test emails from any of the available transports')
                ->setIcon('test')
                ->setWeight(50)
        );
    }
}