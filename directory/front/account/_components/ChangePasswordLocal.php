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

class ChangePasswordLocal extends arch\component\FormUi {

    protected function _execute() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Change password'));

        // Old password
        if(!$this['auth']->isNew()) {
            $fs->addField($this->_('Old password'))->push(
                $this->html->passwordTextbox(
                        $this->fieldName('oldPassword'),
                        $this->values->oldPassword
                    )
                    ->isRequired(true)
            );
        }

        // New password
        $fs->addField($this->_('New password'))->push(
            $this->html->passwordTextbox(
                    $this->fieldName('newPassword'),
                    $this->values->newPassword
                )
                ->isRequired(true)
        );

        // Confirm new password
        $fs->addField($this->_('Confirm new password'))->push(
            $this->html->passwordTextbox(
                    $this->fieldName('confirmNewPassword'),
                    $this->values->confirmNewPassword
                )
                ->isRequired(true)
        );

        // Buttons
        $fs->addDefaultButtonGroup();
    }
}