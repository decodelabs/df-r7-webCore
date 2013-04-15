<?php
echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->request('~admin/users/add', true),
                $this->_('Add new user')
            )
            ->setIcon('add')
            ->addAccessLock('axis://user/Client#add'),

        '|',

        $this->html->link('~admin/users/groups/', $this->_('View groups'))
            ->setIcon('group')
            ->setDisposition('transitive'),

        $this->html->link('~admin/users/roles/', $this->_('View roles'))
            ->setIcon('role')
            ->setDisposition('transitive'),

        '|',

        $this->html->backLink()
    );


echo $this->import->component('UserList', '~admin/users/');