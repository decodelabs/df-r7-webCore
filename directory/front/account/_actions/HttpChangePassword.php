<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpChangePassword extends arch\form\Action {

    const DEFAULT_ACCESS = arch\IAccess::CONFIRMED;

    protected $_auth;

    protected function init() {
        $this->_auth = $this->data->user->auth->fetchLocalClientAdapter();

        if(!$this->_auth) {
            $this->throwError(403, 'Local adapter not found');
        }
    }

    protected function createUi() {
        $this->content->push(
            $this->apex->component('~front/account/ChangePasswordLocal', $this)
        );
    }

    protected function onSaveEvent() {
        $userConfig = $this->data->user->config;

        $validator = $this->data->newValidator()

            // Old password
            ->addRequiredField('oldPassword', 'text')
                ->setCustomValidator(function($node, $value, $field) {
                    $hash = $this->data->hash($value);

                    if($hash != $this->_auth['password']) {
                        $node->addError('incorrect', $this->_(
                            'This password is incorrect'
                        ));
                    }
                })

            // New password
            ->addRequiredField('newPassword', 'password')
                ->setMatchField('confirmNewPassword')
                ->shouldCheckStrength($userConfig->shouldCheckPasswordStrength())
                ->setMinStrength($userConfig->getMinPasswordStrength())

            ->validate($this->values);

        $this->values->oldPassword->setValue('');
        $this->values->newPassword->setValue('');
        $this->values->confirmNewPassword->setValue('');

        return $this->complete(function() use($validator) {
            $this->_auth->password = $validator['newPassword'];
            $this->_auth->save();
            $this->user->refreshClientData();

            $this->comms->flashSuccess(
                'password.change',
                $this->_('Your password has been successfully updated')
            );
        });
    }
}