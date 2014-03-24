<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
use df\axis;
    
class Index_WebCore extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
        $criticalErrorCount = axis\Model::loadUnitFromId('log/criticalError')->select()->count();
        $notFoundCount = axis\Model::loadUnitFromId('log/notFound')->select()->count();
        $accessErrorCount = axis\Model::loadUnitFromId('log/accessError')->select()->count();

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
                ->setWeight(30)
        );
    }
}