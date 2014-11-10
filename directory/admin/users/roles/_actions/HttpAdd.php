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
    
    protected $_role;

    protected function _init() {
        $this->_role = $this->data->newRecord('axis://user/Role');
    }

    protected function _createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Role details'));
        
        // Name
        $fs->addFieldArea($this->_('Name'))
            ->addTextbox('name', $this->values->name)
                ->isRequired(true);
             
        // Bind state   
        $fs->addFieldArea($this->_('Bind state'))
            ->setDescription($this->_('If set, this role will apply to just-logged-in users with this state, regardless of whether they are attached to this role in any other way'))
            ->addSelectList('bindState', $this->values->bindState, [
                '' => $this->_('None'),
                '-1' => $this->_('Deactivated'),
                '0' => $this->_('Guest'),
                '1' => $this->_('Pending activation'),
                '2' => $this->_('Bound'),
                '3' => $this->_('Bound and confirmed')
            ]);


        // Min required state   
        $fs->addFieldArea($this->_('Minimum required state'))
            ->setDescription($this->_('User\'s state must be equal to or higher than this value to aquire this role, no matter how they are attached to it'))
            ->addSelectList('minRequiredState', $this->values->minRequiredState, [
                '' => $this->_('None'),
                '-1' => $this->_('Deactivated'),
                '0' => $this->_('Guest'),
                '1' => $this->_('Pending activation'),
                '2' => $this->_('Bound'),
                '3' => $this->_('Bound and confirmed')
            ]);
            

        // Priority
        $fs->addFieldArea($this->_('Priority'))
            ->addNumberTextbox('priority', $this->values->priority)
                ->isRequired(true)
                ->setRange(0, null, 1);
                
                
        // Buttons
        $fs->push($this->html->defaultButtonGroup());
    }


    protected function _onSaveEvent() {
        $this->data->newValidator()

            // Name
            ->addRequiredField('name', 'text')

            // Bind state
            ->addField('bindState', 'integer')
                ->setRange(-1, 3)

            // Min required state
            ->addField('minRequiredState', 'integer')
                ->setRange(-1, 3)

            // Priority
            ->addRequiredField('priority', 'integer')
                ->setMin(0)

            ->validate($this->values)
            ->applyTo($this->_role);


        if($this->isValid()) {
            $this->_role->save();
            $this->user->instigateGlobalKeyringRegeneration();

            $this->comms->flash(
                'role.save',
                $this->_('The role has been successfully saved'),
                'success'
            );

            return $this->complete();
        }
    }
}
