<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\roles\_formDelegates;

use df;
use df\core;
use df\arch;

class RoleSelector extends arch\form\Delegate {
    
    public function renderFieldSet() {
        $model = $this->data->getModel('user');
        $fs = $this->html->fieldSet($this->_('Roles'));
        
        
        // Search
        $fs->addFieldArea($this->_('Search'))->push(
            $this->html->textbox(
                    $this->fieldName('search'), 
                    $this->values->search
                ),

            $this->html->eventButton(
                    $this->eventName('search'), 
                    $this->_('Search')
                )
                ->shouldValidate(false)
                ->setIcon('search')
        );
        
        
        // Show search results
        if(count($this->values->searchResults)) {
            $searchRoleList = $model->role->fetch()
                ->where('id', 'in', $this->values->searchResults->toArray())
                ->toArray();
                
            if(empty($searchRoleList)) {
                unset($this->values->searchResults);
            } else {
                $fa = $fs->addFieldArea($this->_('Search results'));
                
                foreach($searchRoleList as $role) {
                    $fa->push(
                        $this->html->hidden($this->fieldName('searchResults[]'), $role['id']),
                        $this->html->checkbox(
                                $this->fieldName('roles['.$role['id'].']'),
                                $this->values->roles->has($role['id']),
                                $role['name'].' ('.$role['priority'].')',
                                $role['id']
                            ),
                        $this->html->string('<br />')
                    );
                }
                
                $fa->addEventButton(
                        $this->eventName('select'),
                        $this->_('Add selected')
                    )
                    ->shouldValidate(false)
                    ->setIcon('add');
            }
        }
        
        
        
        // Show selected
        if(count($this->values->roles)) {
            $selectedRoleList = $model->role->fetch()
                ->where('id', 'in', $this->values->roles->getKeys())
                ->toArray();
                
            if(empty($selectedRoleList)) {
                unset($this->values->roles);
            } else {
                $fa = $fs->addFieldArea($this->_('Selected roles'));
                
                foreach($selectedRoleList as $role) {
                    $fa->push(
                        $this->html->hidden(
                                $this->fieldName('roles['.$role['id'].']'), 
                                $role['id']
                            ),

                        $role['name'].' ('.$role['priority'].')',

                        $this->html->eventButton(
                                $this->eventName('removeRole', $role['id']), 
                                $this->_('Remove')
                            )
                            ->shouldValidate(false)
                            ->setIcon('remove'),

                        $this->html->string('<br />')
                    );
                }
            }
        }
        
        return $fs;
    }


    protected function _onSearchEvent() {
        unset($this->values->searchResults);
        
        $search = $this->data->newValidator()
            ->addField('search', 'text')
                ->setSanitizer(function($value) {
                    if(empty($value)) {
                        $value = '*';
                    }

                    return $value;
                })
                ->end()
            ->validate($this->values)
            ->getValue('search');
            
        if($this->values->search->isValid()) {
            $model = $this->data->getModel('user');
            $this->values->searchResults = $model->role->select('id')
                ->beginWhereClause()
                    ->where('name', 'contains', $search)
                    ->orWhere('name', 'like', $search)
                    ->endClause()
                ->where('id', '!in', $this->values->roles->getKeys())
                ->toList('id');
        }
    }
    
    protected function _onSelectEvent() {
        unset($this->values->search, $this->values->searchResults);
    }
    
    protected function _onRemoveRoleEvent($id) {
        unset($this->values->roles->{$id});
    }
    
    
    
    public function setRoleIds(array $ids) {
        foreach($ids as $id) {
            $this->values->roles[$id] = $id;
        }
        
        return $this;
    }
    
    public function getRoleIds() {
        return $this->values->roles->toArray();
    }
}
