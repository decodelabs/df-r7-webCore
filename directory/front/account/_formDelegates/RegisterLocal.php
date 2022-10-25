<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\account\_formDelegates;

use df\user;

class RegisterLocal extends RegisterBase
{
    protected function setDefaultValues(): void
    {
        if ($this->_invite) {
            $this->values->fullName = $this->_invite['name'];
            $parts = explode(' ', $this->_invite['name']);
            $this->values->nickName = array_shift($parts);
            $this->values->email = $this->_invite['email'];
        }
    }

    protected function createUi(): void
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Register an account'));

        // Name
        $fs->addField($this->_('Your full name'))->push(
            $this->html->textbox(
                    $this->fieldName('fullName'),
                    $this->values->fullName
                )
                ->isRequired(true)
        );

        // Email
        $fs->addField($this->_('Email address'))->push(
            $this->html->emailTextbox(
                    $this->fieldName('email'),
                    $this->values->email
                )
                ->isRequired(true)
        );

        // Password
        $fs->addField($this->_('Password'))->push(
            $this->html->passwordTextbox(
                    $this->fieldName('password'),
                    $this->values->password
                )
                ->shouldAutoComplete(false)
                ->isRequired(true)
        );

        // Confirm password
        $fs->addField($this->_('Confirm password'))->push(
            $this->html->passwordTextbox(
                    $this->fieldName('confirmPassword'),
                    $this->values->confirmPassword
                )
                ->shouldAutoComplete(false)
                ->isRequired(true)
        );

        // Recaptcha
        $fs->addField()->push(
            $this->html->recaptcha()
        );

        // Buttons
        $fs->addButtonArea(
            $this->html->eventButton(
                    $this->eventName('register'),
                    $this->_('Create account')
                )
                ->setIcon('accept'),

            $this->html->cancelEventButton()
        );
    }

    protected function onRegisterEvent()
    {
        $client = $this->_createClient();

        $this->data->newValidator()

            // Full name
            ->addRequiredField('fullName', 'text')

            // Nick name
            ->addField('nickName', 'text')

            // Email
            ->addRequiredField('email')
                ->setStorageAdapter($this->data->user->client)
                ->setUniqueErrorMessage($this->_('An account already exists with this email address'))

            // Recaptcha
            ->addField('recaptcha')

            ->validate($this->values)
            ->applyTo($client);

        if (!$this->isValid()) {
            return;
        }

        $auth = $this->_createAuth($client, 'Local');

        $this->data->newValidator()
            ->addRequiredField('password')
                ->setMatchField('confirmPassword')
            ->validate($this->values)
            ->applyTo($auth);

        /** @phpstan-ignore-next-line */
        if ($this->isValid()) {
            $this->_saveClient($client);

            return $this->_completeRegistration(function () use ($auth) {
                $request = new user\authentication\Request('Local');
                $request->setIdentity($auth['identity']);
                $request->setCredential('password', $this->values['password']);
                $request->setAttribute('rememberMe', (bool)true);

                return $request;
            });
        }
    }
}
