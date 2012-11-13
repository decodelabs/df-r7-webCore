<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
    
class Index_WebCore extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
        $entryList->addEntries(
            $entryList->newLink('~admin/users/', 'User management')
                ->setId('users')
                ->setDescription('View, add and edit site users, set up groups and roles and create access keys')
                ->setIcon('user')
                ->setWeight(30),

            $entryList->newLink('~admin/navigation/', 'Navigation')
                ->setId('navigation')
                ->setDescription('Create and modify menus, generate a site map and control how users navigate your site')
                ->setIcon('link')
                ->setWeight(50)
        );
    }
}