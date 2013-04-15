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

echo $this->html->collectionList($this['groupList'])
    ->setErrorMessage('There are no groups to display')

    // Name
    ->addField('name', function($group) {
        return $this->import->component('GroupLink', '~admin/users/groups/', $group)
            ->setRedirectFrom($this->_urlRedirect);
    })
    
    ->addField('users')
    ->addField('roles')
    
    // Actions
    ->addField('actions', function($group) {
        return [
            // Edit
            $this->import->component('GroupLink', '~admin/users/groups/', $this['group'], $this->_('Edit'))
                ->setAction('edit')
                ->addAccessLock($group->getActionLock('edit')),

            // Delete
            $this->import->component('GroupLink', '~admin/users/groups/', $this['group'], $this->_('Delete'))
                ->setAction('delete')
                ->addAccessLock($group->getActionLock('delete')),
        ];
    })
    ;

