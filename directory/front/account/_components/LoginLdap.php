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
use df\aura;

class LoginLdap extends arch\component\template\FormUi {

    protected function _execute() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('LDAP Sign-in'));


        // Username
        $fs->addFieldArea($this->_('Username'))
            ->addTextbox(
                    $this->fieldName('identity'),
                    $this->values->identity
                )
                ->isRequired(true);

        // Password
        $fs->addFieldArea($this->_('Password'))
            ->addPasswordTextbox(
                    $this->fieldName('password'),
                    $this->values->password
                )
                ->isRequired(true);

        // Remember
        $fs->addFieldArea()->push(
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
}