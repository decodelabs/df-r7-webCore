<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\clients\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpChangePassword extends arch\form\Action {

    protected $_auth;

    protected function _init() {
        $this->_auth = $this->data->fetchForAction(
            'axis://user/Auth',
            [
                'user' => $this->request->query['user'],
                'adapter' => 'Local'
            ],
            'edit'
        );
    }

    protected function _getDataId() {
        return $this->_auth->getRawId('user');
    }

    protected function _createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Change password'));

        // User
        $fs->addFieldArea($this->_('User'))->push(
            $this->html->textbox('user', $this->_auth['user']['fullName'])
                ->isDisabled(true)
        );

        // New password
        $fs->addFieldArea($this->_('Password'))->push(
            $this->html->passwordTextbox('password', $this->values->password)
                ->isRequired(true)
        );

        // Confirm password
        $fs->addFieldArea($this->_('Confirm password'))->push(
            $this->html->passwordTextbox('confirmPassword', $this->values->confirmPassword)
                ->isRequired(true)
        );

        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function _onSaveEvent() {
        $this->data->newValidator()
            ->addRequiredField('password', 'password')
                ->setMatchField('confirmPassword')
            ->validate($this->values);

        if($this->isValid()) {
            $this->_auth->password = $this->data->hash($this->values['password']);
            $this->_auth->save();

            $this->comms->flashSuccess(
                'password.change',
                $this->_('The user\'s password has been changed')
            );           

            return $this->complete(); 
        }
    }
}