<?php
echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->request('~admin/users/groups/add', true),
                $this->_('Add new group')
            )
            ->setIcon('add'),

        '|',

        $this->html->backLink()
    );

echo $this->html->collectionList($this['groupList'])
    ->setErrorMessage('There are no groups to display')

    // Name
    ->addField('name', function($row, $view) {
        return $view->html->link('~admin/users/groups/details?group='.$row['id'], $row['name'])
            ->setIcon('group')
            ->setDisposition('informative');
    })
    
    ->addField('users')
    ->addField('roles')
    
    // Actions
    ->addField('actions', function($row, $view) {
        return [
            $view->html->link(
                    $view->uri->request('~admin/users/groups/edit?group='.$row['id'], true),
                    $view->_('Edit')
                )
                ->setIcon('edit'),

            $view->html->link(
                    $view->uri->request('~admin/users/groups/delete?group='.$row['id'], true),
                    $view->_('Delete')
                )
                ->setIcon('delete')
        ];
    })
    ;

