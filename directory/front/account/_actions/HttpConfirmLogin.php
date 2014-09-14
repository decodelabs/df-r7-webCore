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
use df\user;
    
class HttpConfirmLogin extends arch\form\Action {

    //const CHECK_ACCESS = false;
    const DEFAULT_ACCESS = arch\IAccess::BOUND;
    const DEFAULT_EVENT = 'login';
    const DEFAULT_REDIRECT = '/';

    protected function _init() {
        if($this->user->client->isConfirmed()) {
            $this->complete();
            return $this->http->defaultRedirect('account/');
        }
    }

    protected function _createUi() {
        $this->content->push(
            $this->import->component('~front/account/ConfirmLoginLocal', $this)
        );
    }

    protected function _onLoginEvent() {
        if(!$this->values->password->hasValue()) {
            $this->values->password->addError('required', $this->_(
                'Please enter your password'
            ));
        }

        if($this->values->isValid()) {
            $request = new user\authentication\Request('Local');
            $request->setIdentity($this->user->client->getEmail());
            $request->setCredential('password', $this->values['password']);

            $result = $this->user->authenticate($request);

            if(!$result->isValid()) {
                $this->values->password->addError('invalid', $this->_(
                    'The password entered was incorrect'
                ));
            } else {
                return $this->complete('account/');
            }
        }
    }

    protected function _onLogoutEvent() {
        $this->user->logout();
        return $this->complete('account/login');
    }
}