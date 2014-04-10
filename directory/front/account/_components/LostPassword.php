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
    
class LostPassword extends arch\component\template\FormUi {

    protected function _execute() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Password recovery'));

        $fs->push(
            $this->html->flashMessage($this->_(
                'Please enter your email address and you will be sent a link with instructions on resetting your password'
            ))
        );

        // Email
        $fs->addFieldArea($this->_('Email address'))->push(
            $this->html->emailTextbox(
                    $this->fieldName('email'), 
                    $this->values->email
                )
                ->isRequired(true)
        );

        // Buttons
        $fs->addButtonArea()->push(
            $this->html->eventButton(
                    $this->eventName('send'),
                    $this->_('Send')
                )
                ->setIcon('mail')
                ->setDisposition('positive'),

            $this->html->cancelEventButton()
        );
    }
}