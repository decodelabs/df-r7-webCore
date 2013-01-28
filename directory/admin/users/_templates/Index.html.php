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
    ->addField('fullName', $this->_('Name'), function($row) {
        return $this->html->link('~admin/users/details?user='.$row['id'], $row['fullName'])
            ->setIcon('user')
            ->setDisposition('informative');
    })
    
    // Email
    ->addField('email', function($row) {
        return $this->html->link($this->uri->mailto($row['email']), $row['email'])
            ->setIcon('mail')
            ->setDisposition('transitive');
    })
    
    // Status
    ->addField('status', function($row) {
        return $this->context->user->client->stateIdToName($row['status']);
    })

    // Groups
    ->addField('groups')
    
    // Country
    ->addField('country')
    
    // Join date
    ->addField('joinDate', $this->_('Joined'), function($row) {
        return $this->html->date($row['joinDate']);
    })
    
    // Login
    ->addField('loginDate', $this->_('Login'), function($row) {
        if($row['loginDate']) {
            return $this->html->timeSince($row['loginDate']);
        }
    })
    
    // Actions
    ->addField('actions', function($row) {
        return [
            $this->html->link(
                    $this->uri->request('~admin/users/edit?user='.$row['id'], true),
                    $this->_('Edit')
                )
                ->setIcon('edit')
                ->addAccessLock('axis://user/Client#edit'),

            $this->html->link(
                    $this->uri->request('~admin/users/delete?user='.$row['id'], true),
                    $this->_('Delete')
                )
                ->setIcon('delete')
                ->addAccessLock('axis://user/Client#delete')
        ];
    })
    ;