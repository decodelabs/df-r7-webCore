<?php
use df\user;

echo $this->html->menuBar()
    ->addLinks(
        // Edit
        $this->import->component('GroupLink', '~admin/users/groups/', $this['group'], $this->_('Edit group'))
            ->setAction('edit')
            ->addAccessLock($this['group']->getActionLock('edit')),

        // Delete
        $this->import->component('GroupLink', '~admin/users/groups/', $this['group'], $this->_('Delete group'))
            ->setAction('delete')
            ->setRedirectTo('~admin/users/groups/')
            ->addAccessLock($this['group']->getActionLock('delete')),
            
        '|',

        $this->html->backLink()
    );



echo $this->html->attributeList($this['group'])
    ->addField('name')
    ->addField('users', function($row) {
        return $row->users->select()->count();
    });
    


echo $this->html->element('h3', $this->_('Roles'));

    
echo $this->html->collectionList($this['group']->roles->fetch()->orderBy('priority'))
    ->setErrorMessage($this->_('This group has no roles'))
    
    // Name
    ->addField('name', function($row) {
        return $this->html->link('~admin/users/roles/details?role='.$row['id'], $row['name'])
            ->setIcon('role')
            ->setDisposition('informative')
            ->addAccessLock('axis://user/Role');
    })
    
    // State
    ->addField('bindState', $this->_('Bind state'), function($row) {
        if($row['bindState'] !== null) {
            return user\Client::stateIdToName($row['bindState']);
        }
    })

    // Min Req State
    ->addField('minRequiredState', $this->_('Minimum required state'), function($row) {
        if($row['minRequiredState'] !== null) {
            return user\Client::stateIdToName($row['minRequiredState']);
        }
    })
    
    ->addField('priority');
