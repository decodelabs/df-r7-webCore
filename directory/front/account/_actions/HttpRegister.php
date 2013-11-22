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
    
class HttpRegister extends arch\form\Action {

    const DEFAULT_ACCESS = arch\IAccess::GUEST;
    const DEFAULT_EVENT = 'register';

    protected $_invite;

    protected function _init() {
        if($this->user->isLoggedIn()) {
            return $this->http->defaultRedirect('account/');
        }

        if(isset($this->request->query->invite)) {
            $this->_invite = $this->data->fetchForAction(
                'axis://user/Invite',
                ['key' => $this->request->query['invite']]
            );

            if(!$this->_invite['isActive']) {
                $this->comms->flash(
                    'invite.inactive',
                    $this->_(
                        'The invite link you have followed is no longer active'
                    ),
                    'error'
                );

                return $this->http->defaultRedirect('/');
            }
        } else {
            if(!$this->data->user->config->isRegistrationEnabled()) {
                $this->comms->flash(
                    'registration.disabled',
                    $this->_(
                        'Registration for this site is currently disabled'
                    ),
                    'error'
                );

                return $this->http->defaultRedirect('/');
            }
        }
    }

    protected function _getDataId() {
        if($this->_invite) {
            return $this->_invite['key'];
        }

        return parent::_getDataId();
    }

    protected function _setDefaultValues() {
        if($this->_invite) {
            $this->values->fullName = $this->_invite['name'];
            $parts = explode(' ', $this->_invite['name']);
            $this->values->nickName = array_shift($parts);
            $this->values->email = $this->_invite['email'];
        }
    }

    protected function _createUi() {
        $this->content->push(
            $this->import->component(
                'RegisterLocal', 
                '~front/account/', 
                $this
            )
        );
    }

    protected function _onRegisterEvent() {
        $client = $this->data->user->client->newRecord();
        $client->joinDate = 'now';

        $this->data->newValidator()

            // Full name
            ->addField('fullName', 'text')
                ->isRequired(true)
                ->end()

            // Nick name
            ->addField('nickName', 'text')
                ->end()

            // Email
            ->addField('email', 'email')
                ->isRequired(true)
                ->setStorageAdapter($this->data->user->client)
                ->setUniqueErrorMessage($this->_('An account already exists with this email address'))
                ->end()

            ->validate($this->values)
            ->applyTo($client);

        if($this->isValid()) {
            $auth = $this->data->user->auth->newRecord();
            $auth->user = $client;
            $auth->identity = $client['email'];
            $auth->adapter = 'Local';
            $auth->bindDate = 'now';

            $this->data->newValidator()
                // Password
                ->addField('password', 'password')
                    ->setMatchField('confirmPassword')
                    ->isRequired(true)
                    ->end()

                ->validate($this->values)
                ->applyTo($auth);
        }

        if($this->isValid()) {
            if($this->_invite) {
                $client->groups->addList($this->_invite->groups->getRelatedPrimaryKeys());
            }

            $client->save();

            if($this->_invite) {
                $this->data->user->invite->claim($this->_invite, $client);
            }

            $config = $this->data->user->config;

            if($config->shouldLoginOnRegistration()) {
                $request = new user\authentication\Request('Local');
                $request->setIdentity($auth['identity']);
                $request->setCredential('password', $this->values['password']);
                $request->setAttribute('rememberMe', (bool)true);

                $result = $this->user->authenticate($request);

                return $this->complete($config->getRegistrationLandingPage());
            } else {
                $this->comms->flash(
                    'registration.complete',
                    $this->_('Your account has been successfully created'),
                    'success'
                );

                $request = $this->directory->newRequest('account/login');
                $request->setRedirect($this->request->getRedirectFrom(), $config->getRegistrationLandingPage());

                return $this->complete($request);
            }
        }
    }
}