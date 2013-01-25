<?php
use df\user;

echo $this->import->template('elements/Header.html');


echo $this->html->attributeList($this['role'])

    // Name
    //->addField('name')
    
    // Bind state
    ->addField('bindState', $this->_('Bind state'), function($row) {
        if($row['bindState'] !== null) {
            return user\Client::stateIdToName($row['bindState']);
        }
    })

    // Min required state
    ->addField('minRequiredState', $this->_('Minimum required state'), function($row) {
        if($row['minRequiredState'] !== null) {
            return user\Client::stateIdToName($row['minRequiredState']);
        }
    })
    
    ->addField('priority')
    
    // Groups
    ->addField('groups', function($row) {
        $groupList = $row->groups->fetch()->orderBy('Name')->toArray();
        
        if(empty($groupList)) {
            return null;
        }
        
        $output = array();
        
        foreach($groupList as $group) {
            $output[] = $this->html->link(
                    '~admin/users/groups/details?group='.$group['id'],
                    $group['name']
                )
                ->setIcon('group')
                ->setDisposition('informative')
                ->addAccessLock('axis://user/Group');
        }
        
        return $this->html->string(implode(', ', $output));
    });