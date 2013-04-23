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


echo $this->html->form('~admin/users/')->setMethod('get')->push(
    $this->html->fieldSet($this->_('Search'))->push(
        $this->html->searchTextbox('search', $this['search']),
        $this->html->submitButton(null, $this->_('Go'))
            ->setIcon('search')
            ->setDisposition('positive')
    )
);


echo $this->import->component('UserList', '~admin/users/');