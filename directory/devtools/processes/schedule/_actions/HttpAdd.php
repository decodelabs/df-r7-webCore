<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\schedule\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpAdd extends arch\form\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected $_schedule;

    protected function _init() {
        $this->_schedule = $this->data->newRecord(
            'axis://task/Schedule'
        );
    }

    protected function _setDefaultValues() {
        $this->values->environmentMode = '';
        $this->values->priority = 'medium';
        $this->values->isLive = true;
    }

    protected function _createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Scheduled task'));

        // Request
        $fs->addFieldArea($this->_('Request'))->push(
            $this->html->textbox('request', $this->values->request)
                ->isRequired(true)
                ->setMaxLength(1024)
        );

        // Environment mode
        $fs->addFieldArea($this->_('Environment mode'))->push(
            $this->html->radioButtonGroup('environmentMode', $this->values->environmentMode, [
                    '' => $this->_('Active at run time'),
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

        // Minute
        $fs->addFieldArea($this->_('Minute'))->push(
            $this->html->textbox('minute', $this->values->minute)
                ->setMaxLength(128)
                ->setPlaceholder('*')
        );

        // Hour
        $fs->addFieldArea($this->_('Hour'))->push(
            $this->html->textbox('hour', $this->values->hour)
                ->setMaxLength(128)
                ->setPlaceholder('*')
        );

        // Day
        $fs->addFieldArea($this->_('Day'))->push(
            $this->html->textbox('day', $this->values->day)
                ->setMaxLength(128)
                ->setPlaceholder('*')
        );

        // Month
        $fs->addFieldArea($this->_('Month'))->push(
            $this->html->textbox('month', $this->values->month)
                ->setMaxLength(128)
                ->setPlaceholder('*')
        );

        // Weekday
        $fs->addFieldArea($this->_('Day of week'))->push(
            $this->html->textbox('weekday', $this->values->weekday)
                ->setMaxLength(128)
                ->setPlaceholder('*')
        );

        // Is live
        $fs->addFieldArea()->push(
            $this->html->checkbox('isLive', $this->values->isLive, $this->_(
                'This scheduled task is live and will be queued at spool time'
            ))
        );

        // Buttons
        $fs->push($this->html->defaultButtonGroup());
    }

    protected function _onSaveEvent() {
        $this->data->newValidator()

            // Request
            ->addRequiredField('request', 'text')
                ->setMaxLength(1024)

            // Env
            ->addField('environmentMode', 'enum')
                ->setOptions(['development', 'testing', 'production'])

            // Priority
            ->addRequiredField('priority', 'enum')
                ->setType('core/unit/Priority')
                
            // Minute
            ->addField('minute', 'text')
                ->setMaxLength(128)

            // Hour
            ->addField('hour', 'text')
                ->setMaxLength(128)

            // Day
            ->addField('day', 'text')
                ->setMaxLength(128)

            // Month
            ->addField('month', 'text')
                ->setMaxLength(128)

            // Weekday
            ->addField('weekday', 'text')
                ->setMaxLength(128)

            // Is live
            ->addField('isLive', 'boolean')

            ->validate($this->values)
            ->applyTo($this->_schedule);

        if($this->isValid()) {
            if($this->_schedule->isNew()
            || $this->_schedule->hasAnyChanged('minute', 'hour', 'day', 'month', 'weekday')
            || ($this->_schedule->hasChanged('isLive') && $this->_schedule['isLive'] == false)) {
                $this->_schedule->isAuto = false;
            }

            $this->_schedule->save();

            $this->comms->flash(
                'task.schedule',
                $this->_('The task has been successfully scheduled'),
                'success'
            );

            return $this->complete();
        }
    }
}