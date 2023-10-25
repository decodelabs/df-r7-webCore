<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users;

use df\arch;

class HttpScaffold extends arch\scaffold\AreaMenu
{
    public const TITLE = 'User management';
    public const ICON = 'user';

    public function generateIndexMenu($entryList)
    {
        $entryList->addEntries(
            $entryList->newLink('./clients/', 'All users')
                ->setId('clients')
                ->setDescription('Get an overview of all registered users')
                ->setIcon('user')
                ->setWeight(10),
            $entryList->newLink('./groups/', 'Groups')
                ->setId('groups')
                ->setDescription('Add, edit and delete groups of user to control organization and permissions')
                ->setIcon('group')
                ->setWeight(20),
            $entryList->newLink('./roles/', 'Roles')
                ->setId('roles')
                ->setDescription('Define what your users are allowed to do on this site')
                ->setIcon('role')
                ->setWeight(30),
            $entryList->newLink('./invite-requests/', 'Invite requests')
                ->setId('inviteRequests')
                ->setDescription('See who is asking to join the site')
                ->setIcon('key')
                ->setWeight(40),
            $entryList->newLink('./invites/', 'Invites')
                ->setId('invites')
                ->setDescription('View who has been invited to register for an account')
                ->setIcon('mail')
                ->setWeight(50),
            $entryList->newLink('./consents/', 'Cookie consent logs')
                ->setIcon('consent')
                ->setDescription('View a record of logged cookie consent transactions')
                ->setIcon('accept')
                ->setWeight(60),
            $entryList->newLink('~admin/users/logins/', 'Login attempts')
                ->setId('logins')
                ->setDescription('See all logs of recent attempted logins')
                ->setWeight(70)
                ->setIcon('form'),
            $entryList->newLink('./deactivations/', 'Deactivations')
                ->setId('deactivations')
                ->setDescription('See who has decided to deactivate their account')
                ->setIcon('remove')
                ->setWeight(80)
                ->setDisposition('neutral')
        );
    }
}
