<?php
echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->request('~admin/users/add', true),
                $this->_('Add new user')
            )
            ->setIcon('add')
            ->addAccessLock('axis://user/Client#add'),

        '|',

        $this->html->link('~admin/users/groups/', $this->_('View groups'))
            ->setIcon('group')
            ->setDisposition('transitive'),

        $this->html->link('~admin/users/roles/', $this->_('View roles'))
            ->setIcon('role')
            ->setDisposition('transitive'),

        '|',

        $this->html->backLink()
    );

echo $this->html->collectionList($this['clientList'])
    // Name
    ->addField('fullName', $this->_('Name'), function($client) {
        return $this->view->import->component('UserLink', '~admin/users/', $client);
    })
    
    // Email
    ->addField('email', function($client) {
        return $this->html->link($this->uri->mailto($client['email']), $client['email'])
            ->setIcon('mail')
            ->setDisposition('transitive');
    })
    
    // Status
    ->addField('status', function($client) {
        return $this->context->user->client->stateIdToName($client['status']);
    })

    // Groups
    ->addField('groups')
    
    // Country
    ->addField('country')
    
    // Join date
    ->addField('joinDate', $this->_('Joined'), function($client) {
        return $this->html->date($client['joinDate']);
    })
    
    // Login
    ->addField('loginDate', $this->_('Login'), function($client) {
        if($client['loginDate']) {
            return $this->html->timeSince($client['loginDate']);
        }
    })
    
    // Actions
    ->addField('actions', function($client) {
        return [
            $this->html->link(
                    $this->uri->request('~admin/users/edit?user='.$client['id'], true),
                    $this->_('Edit')
                )
                ->setIcon('edit')
                ->addAccessLock('axis://user/Client#edit'),

            $this->html->link(
                    $this->uri->request('~admin/users/delete?user='.$client['id'], true),
                    $this->_('Delete')
                )
                ->setIcon('delete')
                ->addAccessLock('axis://user/Client#delete')
        ];
    })
    ;