<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\queue\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpAdd extends arch\form\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected $_task;

    protected function _init() {
        $this->_task = $this->scaffold->newRecord();
    }

    protected function _setDefaultValues() {
        $this->values->environmentMode = df\Launchpad::getEnvironmentMode();
        $this->values->priority = 'medium';
    }

    protected function _createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Queued task'));

        // Request
        $fs->addFieldArea($this->_('Request'))->push(
            $this->html->textbox('request', $this->values->request)
                ->isRequired(true)
                ->setMaxLength(1024)
        );

        // Environment mode
        $fs->addFieldArea($this->_('Environment mode'))->push(
            $this->html->radioButtonGroup('environmentMode', $this->values->environmentMode, [
                    'development' => $this->_('Development'),
                    'testing' => $this->_('Testing'),
                    'production' => $this->_('Production')
                ])
                ->isRequired(true)
        );

        // Priority
        $fs->addFieldArea($this->_('Priority'))->push(
            $this->html->prioritySlider('priority', $this->values->priority)
                ->isRequired(true)
        );

        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function _onSaveEvent() {
        $this->data->newValidator()

            // Request
            ->addRequiredField('request', 'text')
                ->setMaxLength(1024)

            // Env
            ->addRequiredField('environmentMode', 'enum')
                ->setOptions(['development', 'testing', 'production'])

            // Priority
            ->addRequiredField('priority', 'enum')
                ->setType('core/unit/Priority')

            ->validate($this->values)
            ->applyTo($this->_task);

        if($this->isValid()) {
            $this->_task->save();

            $this->comms->flashSuccess(
                'task.queue',
                $this->_('The task has been successfully queued')
            );

            return $this->complete();
        }
    }
}