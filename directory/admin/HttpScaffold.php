<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpScaffold extends arch\scaffold\template\AreaMenu {
    
    const DIRECTORY_TITLE = 'Admin';
    const DIRECTORY_ICON = 'admin';
    const HEADER_BAR = false;

    public function generateIndexMenu($entryList) {
        $entryList->addEntries(
            $entryList->newLink('./users/', 'User management')
                ->setId('users')
                ->setDescription('View, add and edit site users, set up groups and roles and create access keys')
                ->setIcon('user')
                ->setWeight(30),

            $entryList->newLink('./navigation/', 'Navigation')
                ->setId('navigation')
                ->setDescription('Create and modify menus, generate a site map and control how users navigate your site')
                ->setIcon('link')
                ->setWeight(50),

            $entryList->newLink('./system/', 'System')
                ->setId('system')
                ->setDescription('Control system-wide settings, view logs, etc')
                ->setIcon('controlPanel')
                ->setWeight(60)
        );
    }
}