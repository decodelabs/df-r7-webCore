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

        $form->push(
            $this->html->flashMessage($this->_(
                    'Are you sure you want to deactivate your account?'
                ), 'warning')
                ->setDescription($this->_(
                    'You will no longer be able to log in to this site, and you will need to contact an admin to have your account reinstated!'
                )),

            $this->html->yesNoButtonGroup('deactivate')
        );
    }
}