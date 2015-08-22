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
    
    protected $_group;

    protected function init() {
        $this->_group = $this->scaffold->newRecord();
    }

    protected function loadDelegates() {
        $this->loadDelegate('roles', '../roles/RoleSelector');
    }
    
    protected function createUi() {
        $model = $this->data->getModel('user');
        
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Group details'));
        
        // Name
        $fs->addFieldArea($this->_('Name'))
            ->addTextbox('name', $this->values->name)
                ->setMaxLength(64)
                ->isRequired(true);
            
        // Signifier
        $fs->addFieldArea($this->_('Signifier'))->push(
            $this->html->textbox('signifier', $this->values->signifier)
                ->setMaxLength(32)
        );
                
        // Roles
        $fs->addFieldArea($this->_('Roles'))->push(
            $this->getDelegate('roles')
        );
        
        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function onSaveEvent() {
        $this->data->newValidator()

            // Name
            ->addRequiredField('name', 'text')
                ->setMaxLength(64)

            // Signifier
            ->addField('signifier', 'text')
                ->setMaxLength(32)

            // Roles
            ->addField('roles', 'delegate')
                ->fromForm($this)

            ->validate($this->values)
            ->applyTo($this->_group);
            

        return $this->complete(function() {
            $this->_group->save();
            $this->user->instigateGlobalKeyringRegeneration();

            $this->comms->flashSaveSuccess('group');
        });
    }
}
