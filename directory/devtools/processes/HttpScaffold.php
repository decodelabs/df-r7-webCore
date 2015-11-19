<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpScaffold extends arch\scaffold\AreaMenu {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    const DIRECTORY_TITLE = 'Process manager';
    const DIRECTORY_ICON = 'task';

    public function generateIndexMenu($entryList) {
        $entryList->addEntries(
            $entryList->newLink('~devtools/processes/schedule/', 'Scheduled tasks')
                ->setId('scheduled-tasks')
                ->setDescription('Set tasks to be run at regular intervals')
                ->setIcon('calendar')
                ->setWeight(10),

            $entryList->newLink('~devtools/processes/queue/', 'Queued tasks')
                ->setId('queued-tasks')
                ->setDescription('Get an overview of tasks that have been queued ready for launch')
                ->setIcon('list')
                ->setWeight(20),

            $entryList->newLink('~devtools/processes/logs/', 'Logs')
                ->setId('logs')
                ->setDescription('View detailed logs of previous tasks run by the task manager spool process')
                ->setIcon('log')
                ->setWeight(30),

            $entryList->newLink('~devtools/processes/daemons/', 'Daemons')
                ->setId('daemons')
                ->setDescription('Launch and view status of long running control processes')
                ->setIcon('launch')
                ->setWeight(40)
        );
    }
}