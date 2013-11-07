<?php

echo $this->import->component('DetailHeaderBar', '~admin/users/clients/', $this['client']);


echo $this->html->attributeList($this['client'])
    ->addField('fullName')
    ->addField('nickName')
    ->addField('email', function($row) {
        return $this->html->link($this->uri->mailto($row['email']), $row['email'])
            ->setIcon('mail')
            ->setDisposition('transitive');
    })
    
    ->addField('status', function($row) {
        return $this->context->user->client->stateIdToName($row['status']);
    })
    
    ->addField('country', function($row) {
        return $this->context->i18n->countries->getName($row['country']);
    })
    
    ->addField('language', function($row) {
        return $this->context->i18n->languages->getName($row['language']);
    })
    
    // Join date
    ->addField('joinDate', 'Joined', function($row) {
        return $this->html->date($row['joinDate']);
    })
    
    // Login
    ->addField('loginDate', 'Last login', function($row) {
        if($row['loginDate']) {
            return $this->html->timeSince($row['loginDate']);
        }
    })
    
    // Groups
    ->addField('groups', function($row) {
        $groupList = $row->groups->fetch()->orderBy('Name')->toArray();
        
        if(empty($groupList)) {
            return null;
        }
        
        $output = array();
        
        foreach($groupList as $group) {
            $output[] = $this->import->component('GroupLink', '~admin/users/groups/', $group);
        }
        
        return $this->html->string(implode(', ', $output));
    })
;

