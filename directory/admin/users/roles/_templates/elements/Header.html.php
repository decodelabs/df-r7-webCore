<?php

echo $this->html->menuBar()
    ->addLinks(
        // Edit
        $this->import->component('RoleLink', '~admin/users/roles', $row, $this->_('Edit role'))
            ->setAction('edit')
            ->addAccessLock($this['role']->getActionLock('edit')),

        // Delete
        $this->import->component('RoleLink', '~admin/users/roles', $row, $this->_('Delete role'))
            ->setAction('delete')
            ->setRedirectTo('~admin/users/roles/')
            ->addAccessLock($this['role']->getActionLock('delete')),

        '|',

        $this['menuEntries'],

        '|',

        // Details
        $this->import->component('RoleLink', '~admin/users/roles', $row, $this->_('Details')), 

        // Keys
        $this->import->component('RoleLink', '~admin/users/roles', $row, $this->_('Keys'))
            ->setNote($this['keyCount'] ? '('.$this['keyCount'].')' : null)
            ->setIcon('key')
            ->setAction('keys'),

        '|',

        $this->html->backLink()
    );



echo $this->html->element('h2', $this->_('Role: %n%', ['%n%' => $this['role']['name']]));