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
    
class HttpResetPassword extends arch\form\Action {

    const DEFAULT_ACCESS = arch\IAccess::GUEST;

    protected $_key;
    protected $_auth;

    protected function _init() {
        if($this->user->isLoggedIn()) {
            return $this->_flashError('loggedIn', $this->_(
                'Passwords cannot be reset while you are logged in'
            ));
        }

        $this->_key = $this->data->user->passwordResetKey->fetch()
            ->where('user', '=', $this->request->query['user'])
            ->where('key', '=', $this->request->query['key'])
            ->toRow();

        if(!$this->_key) {
            return $this->_flashError('notFound', $this->_(
                'The password reset key this link refers to no longer exists'
            ));
        }

        if(!$this->_key['user']) {
            $this->throwError(500, 'Client not attached to key');
        }

        if($this->_key->isRedeemed()) {
            return $this->_flashError('alreadyReset', $this->_(
                'The password reset key this link refers to has already been redeemed'
            ));
        }

        if($this->_key['adapter'] != 'Local') {
            $this->throwError(500, 'Password reset key not for local adapter');
        }

        if($this->_key->hasExpired()) {
            return $this->_flashError('expired', $this->_(
                'The password reset key this link refers to has now expired'
            ));
        }

        $this->_auth = $this->data->user->auth->fetch()
            ->where('user', '=', $this->_key['user'])
            ->where('adapter', '=', 'Local')
            ->toRow();

        if(!$this->_auth) {
            $this->throwError(500, 'Local auth domain not found');
        }
    }

    protected function _flashError($flashKey, $message) {
        $this->comms->flashError(
            'passwordResetKey.'.$flashKey,
            $message
        );

        return $this->http->redirect('account/');
    }

    protected function _setDefaultValues() {
        $this->data->user->passwordResetKey->pruneUnusedKeys();
    }

    protected function _createUi() {
        $this->content->push(
            $this->apex->component('~front/account/ResetPassword', $this)
                ->setSlot('key', $this->_key)
        );
    }

    protected function _onSaveEvent() {
        $userConfig = $this->data->user->config;

        $this->data->newValidator()
            ->addRequiredField('newPassword', 'password')
                ->setMatchField('confirmNewPassword')
                ->shouldCheckStrength($userConfig->shouldCheckPasswordStrength())
                ->setMinStrength($userConfig->getMinPasswordStrength())

            ->validate($this->values);

        if($this->isValid()) {
            $this->_auth->password = $this->data->hash($this->values['newPassword']);
            $this->_auth->save();

            $this->_key->resetDate = 'now';
            $this->_key->save();

            $this->data->user->passwordResetKey->deleteRecentUnusedKeys($this->_key);

            $this->comms->flashSuccess(
                'password.reset',
                $this->_('Your password has been updated')
            );

            return $this->complete('account/');
        }
    }
}