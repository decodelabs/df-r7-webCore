<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpSettings extends arch\form\Action {

    protected $_config;

    protected function _init() {
        $this->_config = $this->data->user->config;
    }

    protected function _setDefaultValues() {
        $this->values->import($this->_config->getConfigValues());
    }

    protected function _createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Settings'));

        // Registration
        $fs->addFieldArea()->push(
            $this->html->checkbox('registrationEnabled', $this->values->registrationEnabled, $this->_(
                'Allow new users to register accounts'
            ))
        );

        // Verify
        $fs->addFieldArea()->push(
            $this->html->checkbox('verifyEmail', $this->values->verifyEmail, $this->_(
                'Verify email address of new registrations'
            ))
        );

        // Login on registration
        $fs->addFieldArea()->push(
            $this->html->checkbox('loginOnRegistration', $this->values->loginOnRegistration, $this->_(
                'Automatically log in new users upon completion of registration'
            ))
        );

        // Landing
        $fs->addFieldArea($this->_('Registration landing page'))->setDescription($this->_(
            'Redirect to this request when logging in for the first time (internal request format)'
        ))->push(
            $this->html->textbox('registrationLandingPage', $this->values->registrationLandingPage)
                ->isRequired(true)
        );

        // Check password
        $fs->addFieldArea()->push(
            $this->html->checkbox('checkPasswordStrength', $this->values->checkPasswordStrength, $this->_(
                'Check password strength when users update their details'
            ))
        );

        // Min strength
        $fs->addFieldArea($this->_('Min password strength'))->push(
            $this->html->numberTextbox('minPasswordStrength', $this->values->minPasswordStrength)
                ->isRequired(true)
                ->setRange(10, null, 1)
        );

        // Invite cap
        $fs->addFieldArea($this->_('Default invite cap'))->setDescription($this->_(
            'Cap the number of invites non-admins can send - leave empty for no limit'
        ))->push(
            $this->html->numberTextbox('inviteCap', $this->values->inviteCap)
                ->setMin(1)
        );

        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function _onSaveEvent() {
        $this->data->newValidator()

            // Registration
            ->addField('registrationEnabled', 'boolean')

            // Verify
            ->addField('verifyEmail', 'boolean')

            // Login
            ->addField('loginOnRegistration', 'boolean')

            // Landing
            ->addRequiredField('registrationLandingPage', 'text')

            // Pass check
            ->addField('checkPasswordStrength', 'boolean')

            // Min strength
            ->addRequiredField('minPasswordStrength', 'integer')
                ->setMin(0)

            // Invite cap
            ->addField('inviteCap', 'integer')
                ->setMin(1)

            ->validate($this->values)
            ->applyTo($this->_config->values);

        if($this->isValid()) {
            $this->_config->save();

            $this->comms->flash(
                'config.saved',
                $this->_('Your settings have been successfully updated'),
                'success'
            );

            return $this->complete();
        }
    }
}