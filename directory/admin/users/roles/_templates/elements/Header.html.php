<?php

echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->request('~admin/users/roles/edit?role='.$this['role']['id'], true),
                $this->_('Edit role')
            )
            ->setIcon('edit')
            ->addAccessLock($this['role']->getActionLock('edit')),

        $this->html->link(
                $this->uri->request('~admin/users/roles/delete?role='.$this['role']['id'], true, '~admin/users/roles/'),
                $this->_('Delete role')
            )
            ->setIcon('delete')
            ->addAccessLock($this['role']->getActionLock('delete')),

        '|',

        $this['menuEntries'],

        '|',

        $this->html->link(
                '~admin/users/roles/details?role='.$this['role']['id'],
                $this->_('Details'),
                true
            )
            ->setIcon('details')
            ->setDisposition('informative'),

        $this->html->link(
                '~admin/users/roles/keys?role='.$this['role']['id'],
                $this->_('Keys'),
                true
            )
            ->setNote($this['keyCount'] ? '('.$this['keyCount'].')' : null)
            ->setIcon('key')
            ->setDisposition('informative'),

        '|',

        $this->html->backLink()
    );



echo $this->html->element('h2', $this->_('Role: %n%', ['%n%' => $this['role']['name']]));