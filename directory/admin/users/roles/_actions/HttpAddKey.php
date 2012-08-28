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
    
    const ITEM_NAME = 'key';
    const ENTITY_LOCATOR = 'axis://user/Key';
    
    protected function _loadRecord() {
        $role = $this->data->fetchForAction(
            'axis://user/Role',
            $this->request->query['role'],
            'addKey'
        );
        
        $output = $this->data->newRecord('axis://user/Key');
        $output['role'] = $role;

        return $output;
    }
    
    protected function _getDataId() {
        return $this->_record->role->getRawId();
    }
    
    protected function _setDefaultValues() {
        $this->values->allow = true;
    }
    
    protected function _createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Role key'));

        // Role
        $fs->addFieldArea($this->_('Role'))
            ->addTextbox('role', $this->_record['role']['name'])
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
            ->addRadioButtonGroup('allow', $this->values->allow, array(
                '1' => $this->_('Allow'),
                '0' => $this->_('Deny')
            ));
            
            
        // Buttons
        $fs->push($this->html->defaultButtonGroup());
    }

    protected function _addValidatorFields(core\validate\IHandler $validator) {
        $validator

            // Domain
            ->addField('domain', 'text')
                ->setSanitizer(function($value) {
                    return strtolower($value);
                })
                ->isRequired(true)
                ->end()

            // Pattern
            ->addField('pattern', 'text')
                ->isRequired(true)
                ->end()

            // Allow
            ->addField('allow', 'boolean')
                ->isRequired(true)
                ->end();
    }
            
    protected function _saveRecord() {
        $this->_record->save();
        $this->user->instigateGlobalKeyringRegeneration();
    }
}
