<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\groups\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpAdd extends arch\form\template\EditRecord {
    
    const ITEM_NAME = 'group';
    const ENTITY_LOCATOR = 'axis://user/Group';
    
    protected function _setupDelegates() {
        $this->loadDelegate('roles', 'RoleSelector', '~admin/users/roles/');
    }
    
    protected function _createUi() {
        $model = $this->data->getModel('user');
        
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Group details'));
        
        // Name
        $fs->addFieldArea($this->_('Name'))
            ->addTextbox('name', $this->values->name)
                ->isRequired(true);
                
                
        // Roles
        $form->push($this->getDelegate('roles')->renderFieldSet($this->_('Roles')));

        
        // Buttons
        $form->push($this->html->defaultButtonGroup());
    }

    protected function _addValidatorFields(core\validate\IHandler $validator) {
        $validator

            // Name
            ->addField('name', 'text')
                ->isRequired(true)
                ->end();
    }
    
    protected function _prepareRecord() {
        $this->_record['roles'] = $this->getDelegate('roles')->apply();
    }

    protected function _saveRecord() {
        $this->_record->save();
        $this->user->instigateGlobalKeyringRegeneration();
    }
}
