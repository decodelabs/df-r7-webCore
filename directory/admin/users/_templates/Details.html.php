<?php
echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->request('~admin/users/edit?user='.$this['client']['id'], true),
                $this->_('Edit user')
            )
            ->setIcon('edit')
            ->addAccessLock($this['client']->getActionLock('edit')),

        $this->html->link(
                $this->uri->request(
                    '~admin/users/delete?user='.$this['client']['id'], true,
                    '~admin/users/'
                ),
                $this->_('Delete user')
            )
            ->setIcon('delete')
            ->addAccessLock($this['client']->getActionLock('delete')),

        '|',

        $this->html->backLink()
    );



echo $this->html->attributeList($this['client'])
    ->addField('fullName')
    ->addField('nickName')
    ->addField('email', function($row, $view) {
        return $view->html->link($view->uri->mailto($row['email']), $row['email'])
            ->setIcon('mail')
            ->setDisposition('transitive');
    })
    
    ->addField('status', function($row, $view) {
        return $view->context->user->client->stateIdToName($row['status']);
    })
    
    ->addField('country', function($row, $view) {
        return $view->context->i18n->countries->getName($row['country']);
    })
    
    ->addField('language', function($row, $view) {
        return $view->context->i18n->languages->getName($row['language']);
    })
    
    // Join date
    ->addField('joinDate', 'Joined', function($row, $view) {
        return $view->format->date($row['joinDate']);
    })
    
    // Login
    ->addField('loginDate', 'Last login', function($row, $view) {
        if($row['loginDate']) {
            return $view->format->timeSince($row['loginDate']);
        }
    })
    
    // Groups
    ->addField('groups', function($row, $view) {
        $groupList = $row->groups->fetch()->orderBy('Name')->toArray();
        
        if(empty($groupList)) {
            return null;
        }
        
        $output = array();
        
        foreach($groupList as $group) {
            $output[] = $view->html->link(
                    '~admin/users/groups/details?group='.$group['id'],
                    $group['name']
                )
                ->setIcon('group')
                ->setDisposition('transitive');
        }
        
        return $view->html->string(implode(', ', $output));
    })
;


echo $this->html->element('h3', $this->_('Authentication adapters'));


echo $this->html->collectionList($this['client']->authDomains->fetch())
    ->addField('adapter')
    ->addField('identity')
    ->addField('bindDate', function($row, $view) {
        return $view->format->date($row['bindDate']);
    })
    ->addField('loginDate', $this->_('Last login'), function($row, $view) {
        if($row['loginDate']) {
            return $view->format->timeSince($row['loginDate']);
        }
    });
