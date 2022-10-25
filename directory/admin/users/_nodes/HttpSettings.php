<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\_nodes;

use df\arch;

class HttpSettings extends arch\node\Form
{
    protected $_config;

    protected function init(): void
    {
        $this->_config = $this->data->user->config;
    }

    protected function setDefaultValues(): void
    {
        $this->values->import($this->_config->getConfigValues());
    }

    protected function createUi(): void
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Settings'));

        // Registration
        $fs->addField()->push(
            $this->html->checkbox('registrationEnabled', $this->values->registrationEnabled, $this->_(
                'Allow new users to register accounts'
            ))
        );

        // Verify
        $fs->addField()->push(
            $this->html->checkbox('verifyEmail', $this->values->verifyEmail, $this->_(
                'Verify email address of new registrations'
            ))
        );

        // Login on registration
        $fs->addField()->push(
            $this->html->checkbox('loginOnRegistration', $this->values->loginOnRegistration, $this->_(
                'Automatically log in new users upon completion of registration'
            ))
        );

        // Landing
        $fs->addField($this->_('Registration landing page'))->setDescription($this->_(
            'Redirect to this request when logging in for the first time (internal request format)'
        ))->push(
            $this->html->textbox('registrationLandingPage', $this->values->registrationLandingPage)
                ->isRequired(true)
        );

        // Check password
        $fs->addField()->push(
            $this->html->checkbox('checkPasswordStrength', $this->values->checkPasswordStrength, $this->_(
                'Check password strength when users update their details'
            ))
        );

        // Min strength
        $fs->addField($this->_('Min password strength'))->push(
            $this->html->numberTextbox('minPasswordStrength', $this->values->minPasswordStrength)
                ->isRequired(true)
                ->setRange(10, null, 1)
        );

        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function onSaveEvent()
    {
        $this->data->newValidator()

            // Registration
            ->addRequiredField('registrationEnabled', 'boolean')

            // Verify
            ->addRequiredField('verifyEmail', 'boolean')

            // Login
            ->addRequiredField('loginOnRegistration', 'boolean')

            // Landing
            ->addRequiredField('registrationLandingPage', 'text')

            // Pass check
            ->addRequiredField('checkPasswordStrength', 'boolean')

            // Min strength
            ->addRequiredField('minPasswordStrength', 'integer')
                ->setMin(0)

            ->validate($this->values)
            ->applyTo($this->_config->values);

        return $this->complete(function () {
            $this->_config->save();

            $this->comms->flashSuccess(
                'config.save',
                $this->_('Your settings have been successfully updated')
            );
        });
    }
}
