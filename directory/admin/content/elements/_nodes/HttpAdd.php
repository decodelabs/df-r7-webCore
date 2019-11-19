<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\elements\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpAdd extends arch\node\Form
{
    protected $_element;

    protected function init()
    {
        $this->_element = $this->scaffold->newRecord([
            'owner' => $this->user->client->getId()
        ]);
    }

    protected function loadDelegates()
    {
        $this->loadDelegate('body', '~/nightfire/ContentSlot')
            ->setCategory('Description')
            ->isRequired(true);
    }

    protected function createUi()
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Element details'));

        // Name
        $fs->addField($this->_('Name'))->push(
            $this->html->textbox('name', $this->values->name)
                ->isRequired(true)
        );

        // Slug
        $fs->addField($this->_('Slug'))->push(
            $this->html->textbox('slug', $this->values->slug)
                ->setPlaceholder($this->_('Auto-generate from name'))
        );

        // Body
        $fs->addField($this->_('Body'))->push(
            $this['body']
        );

        // Buttons
        $form->addDefaultButtonGroup();
    }

    protected function onSaveEvent()
    {
        $this->data->newValidator()

            // Name
            ->addRequiredField('name', 'text')

            // Slug
            ->addRequiredField('slug')
                ->setDefaultValueField('name')
                ->setRecord($this->_element)

            // Body
            ->addField('body', 'delegate')
                ->fromForm($this)

            ->validate($this->values)
            ->applyTo($this->_element);

        return $this->complete(function () {
            if (!$this->_element->isNew()) {
                $this->_element->lastEditDate = 'now';
            }

            $this->_element->save();
            $this->comms->flashSaveSuccess('element');
        });
    }
}
