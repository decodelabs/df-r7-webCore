<?php

echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->request('~admin/users/edit?user='.$this['client']['id'], true),
                $this->_('Edit user')
            )
            ->setIcon('edit')
            ->addAccessLock($this['client']->getActionLock('edit')),

        $this->html->link(
                $this->uri->request(
                    '~admin/users/delete?user='.$this['client']['id'], true,
                    '~admin/users/'
                ),
                $this->_('Delete user')
            )
            ->setIcon('delete')
            ->addAccessLock($this['client']->getActionLock('delete')),

        '|',

        $this['menuEntries'],

        '|',

        $this->html->link(
                '~admin/users/details?user='.$this['client']['id'],
                $this->_('Details'),
                true
            )
            ->setIcon('details')
            ->setDisposition('informative'),

        $this->html->link(
                '~admin/users/authentication?user='.$this['client']['id'],
                $this->_('Authentication'),
                true
            )
            ->setNote($this->format->counterNote($this['authenticationCount']))
            ->setIcon('user')
            ->setDisposition('informative'),

        '|',

        $this->html->backLink()
    );