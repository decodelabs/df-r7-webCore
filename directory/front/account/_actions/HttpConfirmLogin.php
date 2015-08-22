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

    const DEFAULT_ACCESS = arch\IAccess::BOUND;
    const DEFAULT_EVENT = 'login';
    const DEFAULT_REDIRECT = '/';

    protected function init() {
        if($this->user->client->isConfirmed()) {
            $this->setComplete();
            return $this->http->defaultRedirect('account/');
        }
    }

    protected function createUi() {
        $this->content->push(
            $this->apex->component('~front/account/ConfirmLoginLocal', $this)
        );
    }

    protected function onLoginEvent() {
        if(!$this->values->password->hasValue()) {
            $this->values->password->addError('required', $this->_(
                'Please enter your password'
            ));
        }

        return $this->complete(function() {
            $request = new user\authentication\Request('Local');
            $request->setIdentity($this->user->client->getEmail());
            $request->setCredential('password', $this->values['password']);

            $result = $this->user->authenticate($request);

            if(!$result->isValid()) {
                $this->values->password->addError('invalid', $this->_(
                    'The password entered was incorrect'
                ));

                return false;
            }

            return 'account/';
        });
    }

    protected function onLogoutEvent() {
        return $this->complete(function() {
            $this->user->logout();
            return 'account/login';
        });
    }
}