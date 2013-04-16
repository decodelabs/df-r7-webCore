<?php
use df\user;

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

        $this->html->backLink()
    );



echo $this->html->attributeList($this['group'])
    ->addField('name')
    ->addField('users', function($group) {
        return $group->users->select()->count();
    });
    


echo $this->html->element('h3', $this->_('Roles'));


echo $this->import->component('RoleList', '~admin/users/roles/', [
        'actions' => false
    ]);