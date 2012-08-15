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
    ->addField('name', function($row) {
        return $this->html->link('~admin/users/groups/details?group='.$row['id'], $row['name'])
            ->setIcon('group')
            ->setDisposition('informative');
    })
    
    ->addField('users')
    ->addField('roles')
    
    // Actions
    ->addField('actions', function($row) {
        return [
            $this->html->link(
                    $this->uri->request('~admin/users/groups/edit?group='.$row['id'], true),
                    $this->_('Edit')
                )
                ->setIcon('edit')
                ->addAccessLock('axis://user/Group#edit'),

            $this->html->link(
                    $this->uri->request('~admin/users/groups/delete?group='.$row['id'], true),
                    $this->_('Delete')
                )
                ->setIcon('delete')
                ->addAccessLock('axis://user/Group#delete')
        ];
    })
    ;

