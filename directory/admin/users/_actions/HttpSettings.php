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

        // Invite cap
        $fs->addFieldArea($this->_('Invite cap'))->setDescription($this->_(
            'Cap the number of invites non-admins can send - leave empty for no limit'
        ))->push(
            $this->html->numberTextbox('inviteCap', $this->values->inviteCap)
                ->setMin(1)
        );

        // Buttons
        $fs->push($this->html->defaultButtonGroup());
    }

    protected function _onSaveEvent() {
        $this->data->newValidator()

            // Registration
            ->addField('registrationEnabled', 'boolean')
                ->end()

            // Verify
            ->addField('verifyEmail', 'boolean')
                ->end()

            // Login
            ->addField('loginOnRegistration', 'boolean')
                ->end()

            // Landing
            ->addField('registrationLandingPage', 'text')
                ->isRequired(true)
                ->end()

            // Invite cap
            ->addField('inviteCap', 'integer')
                ->setMin(1)
                ->end()

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