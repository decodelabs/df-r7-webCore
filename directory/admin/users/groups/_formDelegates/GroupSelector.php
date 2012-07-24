<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\groups\_formDelegates;

use df;
use df\core;
use df\arch;

class GroupSelector extends arch\form\Delegate {
    
    public function renderFieldSet() {
        $model = $this->data->getModel('user');
        $fs = $this->html->fieldSet($this->_('Groups'));
        
        
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
            $searchGroupList = $model->group->fetch()
                ->where('id', 'in', $this->values->searchResults->toArray())
                ->toArray();    
                
            if(empty($searchGroupList)) {
                unset($this->values->searchResults);
            } else {
                $fa = $fs->addFieldArea($this->_('Search results'));
                
                foreach($searchGroupList as $group) {
                    $fa->push(
                        $this->html->hidden($this->fieldName('searchResults[]'), $group['id']),
                        $this->html->checkbox(
                                $this->fieldName('groups['.$group['id'].']'),
                                $this->values->groups->has($group['id']),
                                $group['name'],
                                $group['id']
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
        if(count($this->values->groups)) {
            $selectedGroupList = $model->group->fetch()
                ->where('id', 'in', $this->values->groups->getKeys())
                ->toArray();
                
            if(empty($selectedGroupList)) {
                unset($this->values->groups);
            } else {
                $fa = $fs->addFieldArea($this->_('Selected groups'));
                
                foreach($selectedGroupList as $group) {
                    $fa->push(
                        $this->html->hidden(
                                $this->fieldName('groups['.$group['id'].']'), 
                                $group['id']
                            ),

                        $group['name'],

                        $this->html->eventButton(
                                $this->eventName('removeGroup', $group['id']), 
                                $this->_('Remove')
                            )
                            ->shouldValidate(false)
                            ->setIcon('remove')
                            ,
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
            $this->values->searchResults = $model->group->select('id')
                ->beginWhereClause()
                    ->where('name', 'contains', $search)
                    ->orWhere('name', 'like', $search)
                    ->endClause()
                ->where('id', '!in', $this->values->groups->getKeys())
                ->toList('id');
        }
    }
    
    protected function _onSelectEvent() {
        unset($this->values->search, $this->values->searchResults);
    }
    
    protected function _onRemoveGroupEvent($id) {
        unset($this->values->groups->{$id});
    }
    
    
    
    public function setGroupIds(array $ids) {
        foreach($ids as $id) {
            $this->values->groups[$id] = $id;
        }
        
        return $this;
    }
    
    public function getGroupIds() {
        return $this->values->groups->toArray();
    }
}
