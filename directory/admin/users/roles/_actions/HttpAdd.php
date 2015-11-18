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

    protected function init() {
        $this->_role = $this->scaffold->newRecord();
    }

    protected function createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Role details'));

        // Name
        $fs->addField($this->_('Name'))
            ->addTextbox('name', $this->values->name)
                ->setMaxLength(64)
                ->isRequired(true);

        // Signifier
        $fs->addField($this->_('Signifier'))->push(
            $this->html->textbox('signifier', $this->values->signifier)
                ->setMaxLength(32)
        );

        // Priority
        $fs->addField($this->_('Priority'))
            ->addNumberTextbox('priority', $this->values->priority)
                ->isRequired(true)
                ->setRange(0, null, 1);


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

            // Priority
            ->addRequiredField('priority', 'integer')
                ->setMin(0)

            ->validate($this->values)
            ->applyTo($this->_role);


        return $this->complete(function() {
            $this->_role->save();
            $this->user->instigateGlobalKeyringRegeneration();

            $this->comms->flashSaveSuccess('role');
        });
    }
}
