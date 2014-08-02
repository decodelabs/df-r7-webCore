<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\tasks;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpScaffold extends arch\scaffold\template\AreaMenu {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    
    const DIRECTORY_TITLE = 'Task manager';
    const DIRECTORY_ICON = 'task';

    public function generateIndexMenu($entryList) {
        $entryList->addEntries(
            $entryList->newLink('~devtools/tasks/schedule/', 'Scheduled tasks')
                ->setId('scheduled-tasks')
                ->setDescription('Set tasks to be run at regular intervals')
                ->setIcon('calendar')
                ->setWeight(10),

            $entryList->newLink('~devtools/tasks/queue/', 'Queued tasks')
                ->setId('queued-tasks')
                ->setDescription('Get an overview of tasks that have been queued ready for launch')
                ->setIcon('list')
                ->setWeight(20),

            $entryList->newLink('~devtools/tasks/logs/', 'Logs')
                ->setId('logs')
                ->setDescription('View detailed logs of previous tasks run by the task manager spool process')
                ->setIcon('log')
                ->setWeight(30)
        );
    }
}