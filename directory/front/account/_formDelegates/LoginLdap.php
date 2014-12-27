<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_formDelegates;

use df;
use df\core;
use df\apex;
use df\arch;
use df\user;

class LoginLdap extends arch\form\Delegate implements arch\form\IParentUiHandlerDelegate {
    
    public function renderUi() {
        $this->content->push(
            $this->apex->component('~front/account/LoginLdap', $this)
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
            $request = new user\authentication\Request('Ldap');
            $request->setIdentity($this->values['identity']);
            $request->setCredential('password', $this->values['password']);
            $request->setAttribute('rememberMe', (bool)$this->values['rememberMe']);

            $result = $this->user->authenticate($request);

            if(!$result->isValid()) {
                $this->values->identity->addError('invalid', $this->_(
                    'The username or password entered was incorrect'
                ));

                $this->values->password->setValue('');
            } else {
                return $this->complete();
            }
        }
    }
}