<?php
use df\user;

echo $this->html->element('section')->setId('section-roleAttributes')->push(
    $this->html->menuBar()
        ->addLinks(
            $this->html->link(
                    $this->uri->request('~admin/users/roles/edit?role='.$this['role']['id'], true),
                    $this->_('Edit role')
                )
                ->setIcon('edit')
                ->addAccessLock($this['role']->getActionLock('edit')),

            $this->html->link(
                    $this->uri->request('~admin/users/roles/delete?role='.$this['role']['id'], true, '~admin/users/roles/'),
                    $this->_('Delete role')
                )
                ->setIcon('delete')
                ->addAccessLock($this['role']->getActionLock('delete')),

            '|',

            $this->html->backLink()
        ),


    $this->html->attributeList($this['role'])
        ->addField('name')
        
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
        })
);


    
echo $this->html->element('section')->setId('section-roleKeys')->push(
    $this->html->element('h3', $this->_('Keys')),

    $this->html->menuBar()
        ->addLinks(
            $this->html->link(
                    $this->uri->request('~admin/users/roles/add-key?role='.$this['role']['id'], true),
                    $this->_('Add new key')
                )
                ->setIcon('add')
                ->addAccessLock('axis://user/Key#add')
        ),
        
        
    $this->html->collectionList($this['role']->keys->fetch()->orderBy('domain'))
        ->setErrorMessage($this->_('This role currently has no keys'))
        ->addField('domain', function($row) {
            return $this->html->link('#', $row['domain'])
                ->setIcon('key');
        })
        ->addField('pattern')
        ->addField('allow', 'Policy', function($row) {
            return $row['allow'] ? 'Allow' : 'Deny';
        })
        
        // Actions
        ->addField('actions', function($row) {
            return array(
                $this->html->link(
                        $this->uri->request('~admin/users/roles/edit-key?key='.$row['id'], true),
                        $this->_('Edit')
                    )
                    ->setIcon('edit')
                    ->addAccessLock('axis://user/Key#edit'),

                $this->html->link(
                        $this->uri->request('~admin/users/roles/delete-key?key='.$row['id'], true),
                        $this->_('Delete')
                    )
                    ->setIcon('delete')
                    ->addAccessLock('axis://user/Key#delete')
            );
        })
);
