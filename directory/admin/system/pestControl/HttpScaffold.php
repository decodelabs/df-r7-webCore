<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\pestControl;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpScaffold extends arch\scaffold\template\AreaMenu {
    
    const DIRECTORY_TITLE = 'Pest control';
    const DIRECTORY_ICON = 'bug';

    public function generateIndexMenu($entryList) {
        $criticalErrorCount = $this->data->pestControl->errorLog->select()->count();
        $notFoundCount = $this->data->pestControl->missLog->select()->count();
        $accessErrorCount = $this->data->pestControl->accessLog->select()->count();

        $entryList->addEntries(
            $entryList->newLink('./errors/', 'Critical errors')
                ->setId('errors')
                ->setDescription('Get detailed information on critical errors encountered by users')
                ->setIcon('error')
                ->setNote($this->format->counterNote($criticalErrorCount))
                ->setWeight(10),

            $entryList->newLink('./misses/', '404 errors')
                ->setId('misses')
                ->setDescription('View requests that users are making to files and actions that don\'t exist')
                ->setIcon('brokenLink')
                ->setNote($this->format->counterNote($notFoundCount))
                ->setWeight(20),

            $entryList->newLink('./access/', 'Access errors')
                ->setId('access')
                ->setDescription('See who is trying to access things they are not supposed to')
                ->setIcon('lock')
                ->setNote($this->format->counterNote($accessErrorCount))
                ->setWeight(30)
        );
    }

    public function addIndexOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link($this->uri('./purge', true), $this->_('Purge old logs'))
                ->setIcon('delete')
        );
    }
}