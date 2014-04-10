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
    
class Deactivate extends arch\component\template\FormUi {

    protected function _execute() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('You really want to leave?'));

        $fs->addFlashMessage($this->_(
            'Are you sure you want to deactivate your account?'
        ), 'warning')
        ->setDescription($this->_(
            'You will no longer be able to log in to this site, and you will need to contact an admin to have your account reinstated!'
        ));

        $fs->addFieldArea($this->_('Why do you want to deactivate your account?'))->push(
            $this->html->textbox(
                    $this->fieldName('reason'), 
                    $this->values->reason
                )
                ->setMaxLength(255)
        );

        $fs->addFieldArea($this->_('What could we have done better?'))->push(
            $this->html->textarea(
                    $this->fieldName('comments'), 
                    $this->values->comments
                )
        );

        $fs->addButtonArea(
            $this->html->eventButton(
                    $this->eventName('deactivate'), 
                    $this->_('Deactivate')
                )
                ->setIcon('remove')
                ->setDisposition('negative'),

            $this->html->cancelEventButton()
        );
    }
}