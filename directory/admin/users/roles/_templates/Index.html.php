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


echo $this->html->collectionList($this['roleList'])
    ->setErrorMessage('There are no groups to display')
    
    // Name
    ->addField('name', function($row) {
        return $this->import->component('RoleLink', '~admin/users/roles', $row)
            ->setRedirectFrom($this->_urlRedirect);
    })
    
    // Bind state
    ->addField('bindState', $this->_('Bind state'), function($row) {
        if($row['bindState'] !== null) {
            return user\Client::stateIdToName($row['bindState']);
        }
    })

    // Min required state
    ->addField('minRequiredState', $this->_('Minimum required state'), function($row) {
        if($row['minRequiredState'] !== null) {
            return user\Client::stateIdToName($row['minRequiredState']);
        }
    })
    
    ->addField('priority')
    ->addField('groups')
    ->addField('keys')
    
    // Actions
    ->addField('actions', function($row) {
        return [
            // Edit
            $this->import->component('RoleLink', '~admin/users/roles', $row, $this->_('Edit'))
                ->setAction('edit')
                ->addAccessLock('axis://user/Role#edit'),

            // Delete
            $this->import->component('RoleLink', '~admin/users/roles', $row, $this->_('Delete'))
                ->setAction('delete')
                ->addAccessLock('axis://user/Role#delete')
        ];
    })
    ;
