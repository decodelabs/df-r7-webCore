<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpScaffold extends arch\scaffold\template\AreaMenu {
    
    const DIRECTORY_TITLE = 'System';
    const DIRECTORY_ICON = 'controlPanel';

    public function generateIndexMenu($entryList) {
        $criticalErrorCount = $this->data->log->criticalError->select()->count();
        $notFoundCount = $this->data->log->notFound->select()->count();
        $accessErrorCount = $this->data->log->accessError->select()->count();

        $entryList->addEntries(
            $entryList->newLink('~admin/system/critical-errors/', 'Critical errors ('.$criticalErrorCount.')')
                ->setId('critical-errors')
                ->setDescription('Get detailed information on critical errors encountered by users')
                ->setIcon('bug')
                ->setWeight(10),

            $entryList->newLink('~admin/system/not-found/', '404 errors ('.$notFoundCount.')')
                ->setId('not-found')
                ->setDescription('View requests that users are making to files and actions that don\'t exist')
                ->setIcon('brokenLink')
                ->setWeight(20),

            $entryList->newLink('~admin/system/access-errors/', 'Access errors ('.$accessErrorCount.')')
                ->setId('access-errors')
                ->setDescription('See who is trying to access things they are not supposed to')
                ->setIcon('lock')
                ->setWeight(30),

            $entryList->newLink('~admin/system/geo-ip/', 'Geo IP')
                ->setId('geo-ip')
                ->setDescription('Set up IP lookup tools to find out where your users are')
                ->setIcon('location')
                ->setWeight(40)
        );
    }
}