<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\roles\_nodes;

use df\arch;

class HttpAddKey extends arch\node\Form
{
    protected $_role;
    protected $_key;

    protected function init(): void
    {
        $this->_role = $this->scaffold->getRecord();

        $this->_key = $this->data->newRecord('axis://user/Key', [
            'role' => $this->_role
        ]);
    }

    protected function getInstanceId(): ?string
    {
        return $this->_role['id'];
    }

    protected function setDefaultValues(): void
    {
        $this->values->allow = true;
    }

    protected function createUi(): void
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Role key'));

        // Role
        $fs->addField($this->_('Role'))
            ->addTextbox('role', $this->_role['name'])
                ->isDisabled(true);

        // Domain
        $fs->addField($this->_('Domain'))
            ->addTextbox('domain', $this->values->domain)
                ->isRequired(true);

        // Pattern
        $fs->addField($this->_('Pattern'))
            ->addTextbox('pattern', $this->values->pattern)
                ->isRequired(true);

        // Allow
        $fs->addField($this->_('Policy'))
            ->addRadioGroup('allow', $this->values->allow, [
                '1' => $this->_('Allow'),
                '0' => $this->_('Deny')
            ]);


        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function onSaveEvent()
    {
        $this->data->newValidator()

            // Domain
            ->addRequiredField('domain', 'text')
                ->setSanitizer(function ($value) {
                    return strtolower($value);
                })

            // Pattern
            ->addRequiredField('pattern', 'text')

            // Allow
            ->addRequiredField('allow', 'boolean')

            ->validate($this->values)
            ->applyTo($this->_key);


        return $this->complete(function () {
            $this->_key->save();
            $this->user->instigateGlobalKeyringRegeneration();

            $this->comms->flashSaveSuccess('role key');
        });
    }
}
