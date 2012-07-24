<?php
use df\user;

echo $this->html->element('section')->setId('section-roleAttributes')->push(
    $this->html->menuBar()
        ->addLinks(
            $this->html->link(
                    $this->uri->request('~admin/users/roles/edit?role='.$this['role']['id'], true),
                    $this->_('Edit role')
                )
                ->setIcon('edit'),

            $this->html->link(
                    $this->uri->request('~admin/users/roles/delete?role='.$this['role']['id'], true, '~admin/users/roles/'),
                    $this->_('Delete role')
                )
                ->setIcon('delete'),

            '|',

            $this->html->backLink()
        ),


    $this->html->attributeList($this['role'])
        ->addField('name')
        
        // State
        ->addField('state', 'Bind state', function($row, $view) {
            return user\Client::stateIdToName($row['state']);
        })
        
        ->addField('priority')
        
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
        ),
        
        
    $this->html->collectionList($this['role']->keys->fetch()->orderBy('domain'))
        ->setErrorMessage($this->_('This role currently has no keys'))
        ->addField('domain', function($row, $view) {
            return $view->html->link('#', $row['domain'])
                ->setIcon('key');
        })
        ->addField('pattern')
        ->addField('allow', 'Policy', function($row, $view) {
            return $row['allow'] ? 'Allow' : 'Deny';
        })
        
        // Actions
        ->addField('actions', function($row, $view) {
            return array(
                $view->html->link(
                        $view->uri->request('~admin/users/roles/edit-key?key='.$row['id'], true),
                        $view->_('Edit')
                    )
                    ->setIcon('edit'),

                $view->html->link(
                        $view->uri->request('~admin/users/roles/delete-key?key='.$row['id'], true),
                        $view->_('Delete')
                    )
                    ->setIcon('delete')
            );
        })
);
