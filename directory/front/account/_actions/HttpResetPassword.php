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
    const DEFAULT_EVENT = 'save';

    protected $_key;
    protected $_auth;

    protected function _init() {
        if($this->user->isLoggedIn()) {
            return $this->_notifyError('loggedIn', $this->_(
                'Passwords cannot be reset while you are logged in'
            ));
        }

        $this->_key = $this->data->user->passwordResetKey->fetch()
            ->where('user', '=', $this->request->query['user'])
            ->where('key', '=', $this->request->query['key'])
            ->toRow();

        if(!$this->_key) {
            return $this->_notifyError('notFound', $this->_(
                'The password reset key this link refers to no longer exists'
            ));
        }

        if(!$this->_key['user']) {
            $this->throwError(500, 'Client not attached to key');
        }

        if($this->_key->isRedeemed()) {
            return $this->_notifyError('alreadyReset', $this->_(
                'The password reset key this link refers to has already been redeemed'
            ));
        }

        if($this->_key['adapter'] != 'Local') {
            $this->throwError(500, 'Password reset key not for local adapter');
        }

        if($this->_key->hasExpired()) {
            return $this->_notifyError('expired', $this->_(
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

    protected function _notifyError($notifyKey, $message) {
        $this->comms->notify(
            'passwordResetKey.'.$notifyKey,
            $message,
            'error'
        );

        return $this->http->redirect('account/');
    }

    protected function _setDefaultValues() {
        $this->data->user->passwordResetKey->pruneUnusedKeys();
    }

    protected function _createUi() {
        $this->content->push(
            $this->import->component('ResetPassword', '~front/account/', $this)
                ->setArg('key', $this->_key)
        );
    }

    protected function _onSaveEvent() {
        $this->data->newValidator()
            ->addField('newPassword', 'password')
                ->isRequired(true)
                ->setMatchField('confirmNewPassword')
                ->end()

            ->validate($this->values);

        if($this->isValid()) {
            $this->_auth->password = $this->data->hash($this->values['newPassword']);
            $this->_auth->save();

            $this->_key->resetDate = 'now';
            $this->_key->save();

            $this->data->user->passwordResetKey->deleteRecentUnusedKeys($this->_key);

            $this->comms->notify(
                'password.reset',
                $this->_('Your password has been updated'),
                'success'
            );

            return $this->complete('account/');
        }
    }
}