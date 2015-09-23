<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\media\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpAdd extends arch\form\Action {
    
    protected $_bucket;

    protected function init() {
        $this->_bucket = $this->scaffold->newRecord();
    }

    protected function createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Bucket details'));

        // Name
        $fs->addFieldArea($this->_('Name'))->push(
            $this->html->textbox('name', $this->values->name)
                ->setMaxLength(128)
                ->isRequired(true)
        );
        
        // Slug
        $fs->addFieldArea($this->_('Slug'))->push(
            $this->html->textbox('slug', $this->values->slug)
                ->setPlaceholder($this->_('Auto-generate from name'))
        );

        // Context 1
        $fs->addFieldArea($this->_('Context 1'))->push(
            $this->html->textbox('context1', $this->values->context1)
                ->setPlaceholder('axis://model/Unit')
        );

        // Context 2
        $fs->addFieldArea($this->_('Context 2'))->push(
            $this->html->textbox('context2', $this->values->context2)
                ->setPlaceholder('axis://model/Unit')
        );

        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function onSaveEvent() {
        $this->data->newValidator()

            // Name
            ->addRequiredField('name', 'text')
                ->setMaxLength(128)

            // Context 1
            ->addField('context1', 'entityLocator')

            // Context 2
            ->addField('context2', 'entityLocator')

            // Slug
            ->addRequiredField('slug')
                ->setDefaultValueField('name')
                ->setRecord($this->_bucket)
                ->addFilter(function($clause, $field) {
                    $clause->where('context1', '=', $field->validator['context1']);
                    $clause->where('context2', '=', $field->validator['context2']);
                })

            ->validate($this->values)
            ->applyTo($this->_bucket);

        return $this->complete(function() {
            $this->_bucket->save();
            $this->comms->flashSaveSuccess('bucket');
        });
    }
}