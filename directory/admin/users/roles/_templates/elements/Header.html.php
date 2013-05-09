<?php

echo $this->html->menuBar()
    ->addLinks(
        // Edit
        $this->import->component('RoleLink', '~admin/users/roles/', $this['role'], $this->_('Edit role'))
            ->setAction('edit'),

        // Delete
        $this->import->component('RoleLink', '~admin/users/roles/', $this['role'], $this->_('Delete role'))
            ->setAction('delete')
            ->setRedirectTo('~admin/users/roles/'),

        '|',

        $this['menuEntries'],

        '|',

        // Details
        $this->import->component('RoleLink', '~admin/users/roles/', $this['role'], $this->_('Details'), true), 

        // Keys
        $this->import->component('RoleLink', '~admin/users/roles/', $this['role'], $this->_('Keys'), true)
            ->setNote($this->format->counterNote($this['keyCount']))
            ->setIcon('key')
            ->setAction('keys'),

        '|',

        $this->html->backLink()
    );



echo $this->html->element('h2', $this->_('Role: %n%', ['%n%' => $this['role']['name']]));