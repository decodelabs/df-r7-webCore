<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\roles\_actions;

use df;
use df\core;
use df\arch;

class HttpAdd extends arch\form\Action {
    
    const DEFAULT_EVENT = 'save';
    
    protected $_role;
    
    protected function _init() {
        $model = $this->data->getModel('user');
        $this->_role = $model->role->newRecord();
    }
    
    protected function _createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Role details'));
        
        // Name
        $fs->addFieldArea($this->_('Name'))
            ->addTextbox('name', $this->values->name)
                ->isRequired(true);
             
        // State   
        $fs->addFieldArea($this->_('Bind state'))
            ->addSelectList('state', $this->values->state, array(
                '' => $this->_('None'),
                '-1' => $this->_('Deactivated'),
                '0' => $this->_('Guest'),
                '1' => $this->_('Pending activation'),
                '2' => $this->_('Logged in'),
                '3' => $this->_('Logged in and confirmed')
            ));
            
        // Priority
        $fs->addFieldArea($this->_('Priority'))
            ->addNumberTextbox('priority', $this->values->priority)
                ->isRequired(true)
                ->setMin(0)
                ->setStep(1);
                
                
        // Buttons
        $fs->push($this->html->defaultButtonGroup());
    }


    protected function _onSaveEvent() {
        $this->data->newValidator()
            ->shouldSanitize(true)
            ->addField('name', 'text')
                ->isRequired(true)
                ->end()
            ->addField('state', 'integer')
                ->setMin(-1)
                ->setMax(3)
                ->end()
            ->addField('priority', 'integer')
                ->isRequired(true)
                ->setMin(0)
                ->end()
            ->validate($this->values)
            ->applyTo($this->_role);
            
        if($this->isValid()) {
            $this->_role->save();
            
            $this->arch->notify(
                'role.saved', 
                $this->_('The role has been successfully saved'), 
                'success'
            );
            
            return $this->complete();
        }
    }
}
