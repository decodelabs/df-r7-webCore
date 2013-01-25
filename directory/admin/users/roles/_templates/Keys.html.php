<?php

echo $this->import->template('elements/Header.html')
    ->setArg('menuEntries', [
        $this->html->link(
                $this->uri->request('~admin/users/roles/add-key?role='.$this['role']['id'], true),
                $this->_('Add new key')
            )
            ->setIcon('add')
            ->addAccessLock('axis://user/Key#add')
    ]);

        
echo $this->html->collectionList($this['keyList'])
    ->setErrorMessage($this->_('This role currently has no keys'))

    // Domain
    ->addField('domain', function($row) {
        return $this->html->link('#', $row['domain'])
            ->setIcon('key');
    })

    // Pattern
    ->addField('pattern')

    // Allow
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
    });