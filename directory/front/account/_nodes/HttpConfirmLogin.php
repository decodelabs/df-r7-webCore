<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\user;

class HttpConfirmLogin extends arch\node\Form {

    const DEFAULT_ACCESS = arch\IAccess::BOUND;
    const DEFAULT_EVENT = 'login';
    const DEFAULT_REDIRECT = '/';

    protected function init() {
        if($this->user->client->isConfirmed()) {
            //$this->setComplete();
            //return $this->http->defaultRedirect('account/');
        }
    }

    protected function createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Confirm password'));

        // Identity
        $fs->addField($this->_('User'))
            ->addEmailTextbox(
                    $this->fieldName('name'),
                    $this->user->client->getFullName()
                )
                ->isDisabled(true);

        // Password
        $fs->addField($this->_('Password'))
            ->addPasswordTextbox(
                    $this->fieldName('password'),
                    $this->values->password
                )
                ->isRequired(true);

        // Buttons
        $fs->addButtonArea()->push(
            $this->html->eventButton(
                    $this->eventName('login'),
                    $this->_('Sign in')
                )
                ->setIcon('accept'),

            $this->html->cancelEventButton()
                ->setEvent('logout')
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