<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_formDelegates;

use df;
use df\core;
use df\apex;
use df\arch;
use df\user;

class LoginLocal extends arch\node\form\Delegate implements arch\node\IParentUiHandlerDelegate {

    use arch\node\TForm_ParentUiHandlerDelegate;

    protected function createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Sign-in'));

        // Lost password
        $fs->addField()->push(
            $this->html->link(
                $this->uri('account/lost-password', true),
                $this->_('Forgot your password?')
            )
        );

        // Identity
        $fs->addField($this->_('Email address'))
            ->addEmailTextbox(
                    $this->fieldName('identity'),
                    $this->values->identity
                )
                ->isRequired(true);

        // Password
        $fs->addField($this->_('Password'))
            ->addPasswordTextbox(
                    $this->fieldName('password'),
                    $this->values->password
                )
                ->isRequired(true);

        // Remember
        $fs->addField()->push(
            $this->html->checkbox(
                $this->fieldName('rememberMe'),
                $this->values->rememberMe,
                $this->_('Remember me')
            )
        );

        // Buttons
        $fs->addButtonArea()->push(
            $this->html->eventButton(
                    $this->eventName('login'),
                    $this->_('Sign in')
                )
                ->setIcon('accept'),

            $this->html->cancelEventButton()
        );
    }

    protected function onLoginEvent() {
        if(!$this->values->identity->hasValue()) {
            $this->values->identity->addError('required', $this->_(
                'Please enter your username'
            ));
        }

        if(!$this->values->password->hasValue()) {
            $this->values->password->addError('required', $this->_(
                'Please enter your password'
            ));
        }


        return $this->complete(function() {
            $request = new user\authentication\Request('Local');
            $request->setIdentity($this->values['identity']);
            $request->setCredential('password', $this->values['password']);
            $request->setAttribute('rememberMe', (bool)$this->values['rememberMe']);

            $result = $this->user->authenticate($request);

            if(!$result->isValid()) {
                $this->values->identity->addError('invalid', $this->_(
                    'The email address or password entered was incorrect'
                ));

                $this->values->password->setValue('');
                return false;
            }
        });
    }
}