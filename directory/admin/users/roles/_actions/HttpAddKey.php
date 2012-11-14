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
    
    const DEFAULT_EVENT = 'save';

    protected $_role;
    protected $_key;

    protected function _init() {
        $this->_role = $this->data->fetchForAction(
            'axis://user/Role',
            $this->request->query['role'],
            'addKey'
        );
        
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
            ->addRadioButtonGroup('allow', $this->values->allow, array(
                '1' => $this->_('Allow'),
                '0' => $this->_('Deny')
            ));
            
            
        // Buttons
        $fs->push($this->html->defaultButtonGroup());
    }

    protected function _onSaveEvent() {
        $this->data->newValidator()

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
                ->end()

            ->validate($this->values)
            ->applyTo($this->_key);


        if($this->isValid()) {
            $this->_key->save();
            $this->user->instigateGlobalKeyringRegeneration();

            $this->arch->notify(
                'key.save',
                $this->_('The role key has been successfully saved'),
                'success'
            );

            return $this->complete();
        }   
    }
}
