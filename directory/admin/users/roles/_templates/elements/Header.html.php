<?php

echo $this->html->menuBar()
    ->addLinks(
        // Edit
        $this->import->component('RoleLink', '~admin/users/roles/', $this['role'], $this->_('Edit role'))
            ->setAction('edit')
            ->addAccessLock($this['role']->getActionLock('edit')),

        // Delete
        $this->import->component('RoleLink', '~admin/users/roles/', $this['role'], $this->_('Delete role'))
            ->setAction('delete')
            ->setRedirectTo('~admin/users/roles/')
            ->addAccessLock($this['role']->getActionLock('delete')),

        '|',

        $this['menuEntries'],

        '|',

        // Details
        $this->import->component('RoleLink', '~admin/users/roles/', $this['role'], $this->_('Details'), true), 

        // Keys
        $this->import->component('RoleLink', '~admin/users/roles/', $this['role'], $this->_('Keys'), true)
            ->setNote($this['keyCount'] ? '('.$this['keyCount'].')' : null)
            ->setIcon('key')
            ->setAction('keys'),

        '|',

        $this->html->backLink()
    );



echo $this->html->element('h2', $this->_('Role: %n%', ['%n%' => $this['role']['name']]));