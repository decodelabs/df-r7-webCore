<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\processes\queue\_nodes;

use df\arch;

class HttpAdd extends arch\node\Form
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected $_task;

    protected function init(): void
    {
        $this->_task = $this->scaffold->newRecord();
    }

    protected function setDefaultValues(): void
    {
        $this->values->priority = 'medium';
    }

    protected function createUi(): void
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Queued task'));

        // Request
        $fs->addField($this->_('Request'))->push(
            $this->html->textbox('request', $this->values->request)
                ->isRequired(true)
                ->setMaxLength(1024)
        );

        // Priority
        $fs->addField($this->_('Priority'))->push(
            $this->html->prioritySlider('priority', $this->values->priority)
                ->isRequired(true)
        );

        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function onSaveEvent()
    {
        $this->data->newValidator()

            // Request
            ->addRequiredField('request', 'text')
                ->setMaxLength(1024)

            // Priority
            ->addRequiredField('priority', 'enum')
                ->setType('core/unit/Priority')

            ->validate($this->values)
            ->applyTo($this->_task);


        return $this->complete(function () {
            $this->_task->save();

            $this->comms->flashSuccess(
                'task.queue',
                $this->_('The task has been successfully queued')
            );
        });
    }
}
