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

class LoginLocal extends arch\component\template\FormUi {

    protected function _execute() {
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
}