<?php
use df\user;

echo $this->import->template('elements/Header.html');


echo $this->html->attributeList($this['role'])

    // Name
    //->addField('name')
    
    // Bind state
    ->addField('bindState', $this->_('Bind state'), function($role) {
        if($role['bindState'] !== null) {
            return user\Client::stateIdToName($role['bindState']);
        }
    })

    // Min required state
    ->addField('minRequiredState', $this->_('Minimum required state'), function($role) {
        if($role['minRequiredState'] !== null) {
            return user\Client::stateIdToName($role['minRequiredState']);
        }
    })
    
    // Priority
    ->addField('priority')
    
    // Groups
    ->addField('groups', function($role) {
        $groupList = $role->groups->fetch()->orderBy('Name')->toArray();
        
        if(empty($groupList)) {
            return null;
        }
        
        $output = array();
        
        foreach($groupList as $group) {
            $output[] = $this->import->component('GroupLink', '~admin/users/groups/', $group);
        }
        
        return $this->html->string(implode(', ', $output));
    });