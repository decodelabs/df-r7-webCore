<?php
use df\user;

echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->to('~admin/users/roles/add', true),
                $this->_('Add new role')
            )
            ->setIcon('add')
            ->addAccessLock('axis://user/Role#add'),

        '|',

        $this->html->backLink()
    );


echo $this->import->component('RoleList', '~admin/users/roles/');