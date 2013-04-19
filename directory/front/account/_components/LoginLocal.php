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

        // Identity
        $fs->addFieldArea($this->_('Email address'))
            ->addEmailTextbox('identity', $this->values->identity)
                ->isRequired(true);

        // Password
        $fs->addFieldArea($this->_('Password'))
            ->addPasswordTextbox('password', $this->values->password)
                ->isRequired(true);

        $fs->addFieldArea()->push(
            $this->html->link(
                $this->uri->request('account/lost-password', true), 
                $this->_('Forgot your password?')
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