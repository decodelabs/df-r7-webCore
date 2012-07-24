<?php
use df\user;

echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->to('~admin/users/roles/add', true),
                $this->_('Add new role')
            )
            ->setIcon('add'),

        '|',

        $this->html->backLink()
    );


echo $this->html->collectionList($this['roleList'])
    ->setErrorMessage('There are no groups to display')
    
    // Name
    ->addField('name', function($row, $view) {
        return $view->html->link('~admin/users/roles/details?role='.$row['id'], $row['name'])
            ->setIcon('role')
            ->setDisposition('informative');
    })
    
    // State
    ->addField('state', 'Bind state', function($row, $view) {
        return user\Client::stateIdToName($row['state']);
    })
    
    ->addField('priority')
    ->addField('groups')
    ->addField('keys')
    
    // Actions
    ->addField('actions', function($row, $view) {
        return [
            $view->html->link(
                    $view->uri->request('~admin/users/roles/edit?role='.$row['id'], true),
                    $view->_('Edit') 
                )
                ->setIcon('edit'),

            $view->html->link(
                    $view->uri->request('~admin/users/roles/delete?role='.$row['id'], true),
                    $view->_('Delete')
                )
                ->setIcon('delete')
        ];
    })
    ;
