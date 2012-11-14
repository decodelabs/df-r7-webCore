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

class HttpAdd extends arch\form\Action {
    
    const DEFAULT_EVENT = 'save';

    protected $_group;

    protected function _init() {
        $this->_group = $this->data->newRecord('axis://user/Group');
    }

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

    protected function _onSaveEvent() {
        $this->data->newValidator()

            // Name
            ->addField('name', 'text')
                ->isRequired(true)
                ->end()

            ->validate($this->values)
            ->applyTo($this->_group);


        if($this->isValid()) {
            $this->_group['roles'] = $this->getDelegate('roles')->apply();
            $this->_group->save();
            $this->user->instigateGlobalKeyringRegeneration();

            $this->arch->notify(
                'group.save',
                $this->_('The group has been successfully saved'),
                'success'
            );

            return $this->complete();
        }
    }
}
