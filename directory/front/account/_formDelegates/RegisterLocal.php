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

class RegisterLocal extends RegisterBase {
    
    protected function _setDefaultValues() {
        if($this->_invite) {
            $this->values->fullName = $this->_invite['name'];
            $parts = explode(' ', $this->_invite['name']);
            $this->values->nickName = array_shift($parts);
            $this->values->email = $this->_invite['email'];
        }
    }

    protected function _onRegisterEvent() {
        $client = $this->_createClient();

        $this->data->newValidator()

            // Full name
            ->addRequiredField('fullName', 'text')

            // Nick name
            ->addField('nickName', 'text')

            // Email
            ->addRequiredField('email', 'email')
                ->setStorageAdapter($this->data->user->client)
                ->setUniqueErrorMessage($this->_('An account already exists with this email address'))

            ->validate($this->values)
            ->applyTo($client);

        if($this->isValid()) {
            $auth = $this->_createAuth($client, 'Local');

            $this->data->newValidator()
                ->addRequiredField('password', 'password')
                    ->setMatchField('confirmPassword')
                ->validate($this->values)
                ->applyTo($auth);
        }

        if($this->isValid()) {
            $this->_saveClient($client);

            return $this->_completeRegistration(function() use($auth) {
                $request = new user\authentication\Request('Local');
                $request->setIdentity($auth['identity']);
                $request->setCredential('password', $this->values['password']);
                $request->setAttribute('rememberMe', (bool)true);

                return $request;
            });
        }
    }
}