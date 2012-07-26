<?php
use df\user;

echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->request('~admin/users/groups/edit?group='.$this['group']['id'], true),
                $this->_('Edit group')
            )
            ->setIcon('edit'),

        $this->html->link(
                $this->uri->request(
                    '~admin/users/groups/delete?group='.$this['group']['id'], true,
                    '~admin/users/groups/'
                ),
                $this->_('Delete group')
            )
            ->setIcon('delete'),
            
        '|',

        $this->html->backLink()
    );



echo $this->html->attributeList($this['group'])
    ->addField('name')
    ->addField('users', function($row) {
        return $row->users->select()->count();
    });
    


echo $this->html->element('h3', $this->_('Roles'));

    
echo $this->html->collectionList($this['group']->roles->fetch()->orderBy('priority'))
    ->setErrorMessage($this->_('This group has no roles'))
    
    // Name
    ->addField('name', function($row, $view) {
        return $view->html->link('~admin/users/roles/details?role='.$row['id'], $row['name'])
            ->setIcon('role')
            ->setDisposition('transitive');
    })
    
    // State
    ->addField('bindState', $this->_('Bind state'), function($row, $view) {
        if($row['bindState'] !== null) {
            return user\Client::stateIdToName($row['bindState']);
        }
    })

    // Min Req State
    ->addField('minRequiredState', $this->_('Minimum required state'), function($row, $view) {
        if($row['minRequiredState'] !== null) {
            return user\Client::stateIdToName($row['minRequiredState']);
        }
    })
    
    ->addField('priority');
