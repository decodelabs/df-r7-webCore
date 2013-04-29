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
    
class ResetPassword extends arch\component\template\FormUi {

    protected function _execute() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Reset password'));

        // Email
        $fs->addFieldArea($this->_('Email'))->push(
            $this->html->emailTextbox('email', $this['key']['user']['email'])
                ->isDisabled(true)
        );

        // New password
        $fs->addFieldArea($this->_('New password'))->push(
            $this->html->passwordTextbox('newPassword', $this->values->newPassword)
                ->isRequired(true)
        );

        // Confirm password
        $fs->addFieldArea($this->_('Confirm new password'))->push(
            $this->html->passwordTextbox('confirmNewPassword', $this->values->confirmNewPassword)
                ->isRequired(true)
        );

        // Buttons
        $fs->push($this->html->defaultButtonGroup());
    }
}