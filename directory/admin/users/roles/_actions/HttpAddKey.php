<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\roles\_actions;

use df;
use df\core;
use df\arch;

class HttpAddKey extends arch\form\Action {
    
    protected $_role;
    protected $_key;

    protected function _init() {
        $this->_role = $this->scaffold->getRecord();
        
        $this->_key = $this->data->newRecord('axis://user/Key', [
            'role' => $this->_role
        ]);
    }
    
    protected function _getDataId() {
        return $this->_role['id'];
    }
    
    protected function _setDefaultValues() {
        $this->values->allow = true;
    }
    
    protected function _createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Role key'));

        // Role
        $fs->addFieldArea($this->_('Role'))
            ->addTextbox('role', $this->_role['name'])
                ->isDisabled(true);
        
        // Domain
        $fs->addFieldArea($this->_('Domain'))
            ->addTextbox('domain', $this->values->domain)
                ->isRequired(true);
                
        // Pattern
        $fs->addFieldArea($this->_('Pattern'))
            ->addTextbox('pattern', $this->values->pattern)
                ->isRequired(true);
                
        // Allow
        $fs->addFieldArea($this->_('Policy'))
            ->addRadioButtonGroup('allow', $this->values->allow, [
                '1' => $this->_('Allow'),
                '0' => $this->_('Deny')
            ]);
            
            
        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function _onSaveEvent() {
        $this->data->newValidator()

            // Domain
            ->addRequiredField('domain', 'text')
                ->setSanitizer(function($value) {
                    return strtolower($value);
                })

            // Pattern
            ->addRequiredField('pattern', 'text')

            // Allow
            ->addRequiredField('allow', 'boolean')

            ->validate($this->values)
            ->applyTo($this->_key);


        if($this->isValid()) {
            $this->_key->save();
            $this->user->instigateGlobalKeyringRegeneration();

            $this->comms->flashSaveSuccess('role key');
            return $this->complete();
        }   
    }
}
