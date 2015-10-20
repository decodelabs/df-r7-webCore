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

class ConfirmLoginLocal extends arch\component\template\FormUi {

    protected function _execute() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Confirm password'));

        // Identity
        $fs->addFieldArea($this->_('User'))
            ->addEmailTextbox(
                    $this->fieldName('name'),
                    $this->user->client->getFullName()
                )
                ->isDisabled(true);

        // Password
        $fs->addFieldArea($this->_('Password'))
            ->addPasswordTextbox(
                    $this->fieldName('password'),
                    $this->values->password
                )
                ->isRequired(true);

        // Buttons
        $fs->addButtonArea()->push(
            $this->html->eventButton(
                    $this->eventName('login'),
                    $this->_('Sign in')
                )
                ->setIcon('accept'),

            $this->html->cancelEventButton()
                ->setEvent('logout')
        );
    }
}