<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\accessPasses\_nodes;

use df\arch;

use df\arch\node\form\SelectorDelegate;

class HttpAdd extends arch\node\Form
{
    protected $_pass;

    protected function init(): void
    {
        $this->_pass = $this->scaffold->newRecord();
    }

    protected function loadDelegates(): void
    {
        // User
        $this->loadDelegate('user', '../clients/UserSelector')
            ->as(SelectorDelegate::class)
            ->isForOne(true)
            ->isRequired(true);
    }

    protected function setDefaultValues(): void
    {
        $this->values->duration = '1 hour';
    }

    protected function createUi(): void
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Access pass details'));

        // User
        $fs->addField($this->_('User'))->push(
            $this['user']
        );

        // Expiry
        $fs->addField($this->_('Lasts for'))->push(
            $this->html->duration('duration', $this->values->duration)
                ->isRequired(true)
        );

        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function onSaveEvent()
    {
        $validator = $this->data->newValidator()

            // User
            ->addField('user', 'delegate')
                ->fromForm($this)

            // Duration
            ->addRequiredField('duration')
                ->setMax('1 month')

            ->validate($this->values);

        return $this->complete(function () use ($validator) {
            $this->_pass->user = $validator['user'];
            $this->_pass->expiryDate = $this->date->now()->add($validator['duration']);
            $this->_pass->save();

            $this->comms->flashSaveSuccess('pass');
        });
    }
}
