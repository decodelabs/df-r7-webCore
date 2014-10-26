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
        $this->_task = $this->data->newRecord('axis://task/Queue');
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
        $fs->push($this->html->defaultButtonGroup());
    }

    protected function _onSaveEvent() {
        $this->data->newValidator()

            // Request
            ->addField('request', 'text')
                ->isRequired(true)
                ->setMaxLength(1024)
                ->end()

            // Env
            ->addField('environmentMode', 'enum')
                ->setOptions(['development', 'testing', 'production'])
                ->isRequired(true)
                ->end()

            // Priority
            ->addField('priority', 'enum')
                ->setOptions(core\unit\Priority::getOptions())
                ->isRequired(true)
                ->end()

            ->validate($this->values)
            ->applyTo($this->_task);

        if($this->isValid()) {
            $this->_task->save();

            $this->comms->flash(
                'task.queue',
                $this->_('The task has been successfully queued'),
                'success'
            );

            return $this->complete();
        }
    }
}