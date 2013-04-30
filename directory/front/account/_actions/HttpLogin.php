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
    
class HttpLogin extends arch\form\Action {

    const CHECK_ACCESS = false;
    const DEFAULT_ACCESS = arch\IAccess::GUEST;
    const DEFAULT_EVENT = 'login';
    const DEFAULT_REDIRECT = '/';

    protected function _init() {
        if($this->user->client->isLoggedIn()) {
            $this->complete();
            return $this->http->defaultRedirect('account/');
        }
    }

    protected function _createUi() {
        $this->content->push(
            $this->import->component(
                'LoginLocal', 
                '~front/account/', 
                $this
            )
        );
    }

    protected function _onLoginEvent() {
        if(!$this->values->identity->hasValue()) {
            $this->values->identity->addError('required', $this->_(
                'Please enter your username'
            ));
        }

        if(!$this->values->password->hasValue()) {
            $this->values->password->addError('required', $this->_(
                'Please enter your password'
            ));
        }

        if($this->values->isValid()) {
            $request = new user\authentication\Request('Local');
            $request->setIdentity($this->values['identity']);
            $request->setCredential('password', $this->values['password']);
            $request->setAttribute('rememberMe', (bool)$this->values['rememberMe']);

            $result = $this->user->authenticate($request);

            if(!$result->isValid()) {
                $this->values->identity->addError('invalid', $this->_(
                    'The email address or password entered was incorrect'
                ));
            } else {
                return $this->complete();
            }
        }
    }
}