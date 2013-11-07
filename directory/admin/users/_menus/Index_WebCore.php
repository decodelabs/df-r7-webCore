<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
    
class Index_WebCore extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
        $entryList->addEntries(
            $entryList->newLink('~admin/users/clients/', 'All users')
                ->setId('clients')
                ->setDescription('Get an overview of all registered users')
                ->setIcon('user')
                ->setWeight(10),

            $entryList->newLink('~admin/users/groups/', 'Groups')
                ->setId('groups')
                ->setDescription('Add, edit and delete groups of user to control organization and permissions')
                ->setIcon('group')
                ->setWeight(20),

            $entryList->newLink('~admin/users/roles/', 'Roles')
                ->setId('roles')
                ->setDescription('Define what your users are allowed to do on this site')
                ->setIcon('role')
                ->setWeight(30),

            $entryList->newLink('~admin/users/settings', 'Settings')
                ->setId('setting')
                ->setDescription('Set options for registration and login')
                ->setIcon('settings')
                ->setWeight(100)
        );
    }
}