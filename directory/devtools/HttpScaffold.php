<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpScaffold extends arch\scaffold\template\AreaMenu {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const DIRECTORY_TITLE = 'Devtools';
    const DIRECTORY_ICON = 'debug';
    const HEADER_BAR = false;

    public function generateIndexMenu($entryList) {
        $entryList->addEntries(
            $entryList->newLink('~devtools/users/', 'User setup')
                ->setId('users')
                ->setDescription('Configure root user, authentication adapters and session settings')
                ->setIcon('user')
                ->setWeight(10),

            $entryList->newLink('~devtools/models/', 'Data models')
                ->setId('models')
                ->setDescription('Manage and update database schemas and data')
                ->setIcon('database')
                ->setWeight(20),

            $entryList->newLink('~devtools/tasks/', 'Task manager')
                ->setId('taskManager')
                ->setDescription('Queue and schedule tasks and view logs of previously run processes')
                ->setIcon('task')
                ->setWeight(30),

            $entryList->newLink('~devtools/theme/', 'Theme configuration')
                ->setId('theme')
                ->setDescription('Change settings and define layouts for available site themes')
                ->setIcon('theme')
                ->setWeight(40),

            $entryList->newLink('~devtools/application/', 'Application utilities')
                ->setId('application')
                ->setDescription('View stats, generate testing and production versions etc.')
                ->setIcon('stats')
                ->setWeight(50),

            $entryList->newLink('~devtools/cache/', 'Cache control')
                ->setId('cache')
                ->setDescription('Refresh, clear and view stats for most cache structures your site employs')
                ->setIcon('toolkit')
                ->setWeight(60)
        );
    }
}