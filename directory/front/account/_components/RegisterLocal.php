<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class RegisterLocal extends arch\component\template\FormUi {

    protected function _execute() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Register an account'));

        // Name
        $fs->addFieldArea($this->_('Your name'))->push(
            $this->html->textbox('fullName', $this->values->fullName)
                ->isRequired(true)
        );

        // Email
        $fs->addFieldArea($this->_('Email address'))->push(
            $this->html->emailTextbox('email', $this->values->email)
                ->isRequired(true)
        );

        // Password
        $fs->addFieldArea($this->_('Password'))->push(
            $this->html->passwordTextbox('password', $this->values->password)
                ->shouldAutoComplete(false)
                ->isRequired(true)
        );

        // Confirm password
        $fs->addFieldArea($this->_('Confirm password'))->push(
            $this->html->passwordTextbox('confirmPassword', $this->values->confirmPassword)
                ->shouldAutoComplete(false)
                ->isRequired(true)
        );

        // Buttons
        $fs->push(
            $this->html->eventButton(
                    $this->eventName('register'), 
                    $this->_('Create account')
                )
                ->setIcon('accept'),

            $this->html->cancelEventButton()
        );
    }
}