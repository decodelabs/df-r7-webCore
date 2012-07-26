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
    ->addField('fullName', $this->_('Name'), function($row, $view) {
        return $view->html->link('~admin/users/details?client='.$row['id'], $row['fullName'])
            ->setIcon('user')
            ->setDisposition('informative');
    })
    
    // Email
    ->addField('email', function($row, $view) {
        return $view->html->link($view->uri->mailto($row['email']), $row['email'])
            ->setIcon('mail')
            ->setDisposition('transitive');
    })
    
    // Status
    ->addField('status', function($row, $view) {
        return $view->context->user->client->stateIdToName($row['status']);
    })
    
    // Country
    ->addField('country')
    
    // Join date
    ->addField('joinDate', $this->_('Joined'), function($row, $view) {
        return $view->format->date($row['joinDate']);
    })
    
    // Login
    ->addField('loginDate', $this->_('Login'), function($row, $view) {
        if($row['loginDate']) {
            return $view->format->timeSince($row['loginDate']);
        }
    })
    
    // Actions
    ->addField('actions', function($row, $view) {
        return [
            $view->html->link(
                    $view->uri->request('~admin/users/edit?user='.$row['id'], true),
                    $view->_('Edit')
                )
                ->setIcon('edit')
                ->addAccessLock('axis://user/Client#edit'),

            $view->html->link(
                    $view->uri->request('~admin/users/delete?user='.$row['id'], true),
                    $view->_('Delete')
                )
                ->setIcon('delete')
                ->addAccessLock('axis://user/Client#delete')
        ];
    })
    ;