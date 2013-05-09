<?php

echo $this->html->menuBar()
    ->addLinks(
        // Edit
        $this->import->component('GroupLink', '~admin/users/groups/', $this['group'], $this->_('Edit group'))
            ->setAction('edit'),

        // Delete
        $this->import->component('GroupLink', '~admin/users/groups/', $this['group'], $this->_('Delete group'))
            ->setAction('delete')
            ->setRedirectTo('~admin/users/groups/'),
            
        '|',

        // Details
        $this->import->component('GroupLink', '~admin/users/groups/', $this['group'], $this->_('Details'), true)
            ->setAction('details')
            ->setIcon('details'),

        // Users
        $this->import->component('GroupLink', '~admin/users/groups/', $this['group'], $this->_('Users'), true)
            ->setAction('users')
            ->setIcon('user')
            ->setNote($this->format->counterNote($this['userCount'])),

        '|',

        $this->html->backLink()
    );


echo $this->html->element('h2', $this->_('Group: %n%', ['%n%' => $this['group']['name']]));