<?php
echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->request('~admin/users/groups/add', true),
                $this->_('Add new group')
            )
            ->setIcon('add')
            ->addAccessLock('axis://user/Group#add'),

        '|',

        $this->html->backLink()
    );


echo $this->import->component('GroupList', '~admin/users/groups/');

