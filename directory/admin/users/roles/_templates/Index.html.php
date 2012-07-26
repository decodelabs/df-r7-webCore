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
    ->addField('name', function($row, $view) {
        return $view->html->link('~admin/users/roles/details?role='.$row['id'], $row['name'])
            ->setIcon('role')
            ->setDisposition('informative');
    })
    
    // Bind state
    ->addField('bindState', $this->_('Bind state'), function($row, $view) {
        if($row['bindState'] !== null) {
            return user\Client::stateIdToName($row['state']);
        }
    })

    // Min required state
    ->addField('minRequiredState', $this->_('Minimum required state'), function($row, $view) {
        if($row['minRequiredState'] !== null) {
            return user\Client::stateIdToName($row['minRequiredState']);
        }
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
                ->setIcon('edit')
                ->addAccessLock('axis://user/Role#edit'),

            $view->html->link(
                    $view->uri->request('~admin/users/roles/delete?role='.$row['id'], true),
                    $view->_('Delete')
                )
                ->setIcon('delete')
                ->addAccessLock('axis://user/Role#delete')
        ];
    })
    ;
