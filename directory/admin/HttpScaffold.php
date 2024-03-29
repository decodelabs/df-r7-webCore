<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin;

use df\arch;

class HttpScaffold extends arch\scaffold\AreaMenu
{
    public const TITLE = 'Admin';
    public const ICON = 'admin';
    public const HEADER_BAR = false;

    public function generateIndexMenu($entryList)
    {
        $entryList->addEntries(
            $entryList->newLink('~admin/content/', 'Published content')
                ->setId('content')
                ->setDescription('Publish, view and edit your site\'s curated content')
                ->setIcon('content')
                ->setWeight(10),
            $entryList->newLink('./media/', 'Media library')
                ->setId('media')
                ->setDescription('Keep control over your site\'s media files')
                ->setIcon('folder')
                ->setWeight(20),
            $entryList->newLink('./users/', 'User management')
                ->setId('users')
                ->setDescription('View, add and edit site users, set up groups and roles and create access keys')
                ->setIcon('user')
                ->setWeight(30),
            $entryList->newLink('./system/', 'System')
                ->setId('system')
                ->setDescription('Control system-wide settings, view logs, etc')
                ->setIcon('controlPanel')
                ->setWeight(50)
        );
    }
}
