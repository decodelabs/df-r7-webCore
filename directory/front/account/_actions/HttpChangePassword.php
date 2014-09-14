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

    protected function _init() {
        $this->_auth = $this->data->user->auth->fetchLocalClientAdapter();

        if(!$this->_auth) {
            $this->throwError(403, 'Local adapter not found');
        }
    }

    protected function _createUi() {
        $this->content->push(
            $this->import->component('~front/account/ChangePasswordLocal', $this)
        );
    }

    protected function _onSaveEvent() {
        $userConfig = $this->data->user->config;

        $validator = $this->data->newValidator()

            // Old password
            ->addField('oldPassword', 'text')
                ->isRequired(true)
                ->setCustomValidator(function($node, $value, $field) {
                    $hash = $this->data->hash($value);

                    if($hash != $this->_auth['password']) {
                        $node->addError('incorrect', $this->_(
                            'This password is incorrect'
                        ));
                    }
                })
                ->end()

            // New password
            ->addField('newPassword', 'password')
                ->isRequired(true)
                ->setMatchField('confirmNewPassword')
                ->shouldCheckStrength($userConfig->shouldCheckPasswordStrength())
                ->setMinStrength($userConfig->getMinPasswordStrength())
                ->end()

            ->validate($this->values);

        $this->values->oldPassword->setValue('');
        $this->values->newPassword->setValue('');
        $this->values->confirmNewPassword->setValue('');
            
        if($this->isValid()) {
            $this->_auth->password = $validator['newPassword'];
            $this->_auth->save();
            $this->user->refreshClientData();

            $this->comms->flash(
                'password.change',
                $this->_('Your password has been successfully updated'),
                'success'
            );

            return $this->complete();
        }
    }
}