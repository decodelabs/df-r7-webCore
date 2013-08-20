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
    
class HttpRegister extends arch\form\Action {

    const DEFAULT_ACCESS = arch\IAccess::GUEST;
    const DEFAULT_EVENT = 'register';

    protected function _init() {
        if($this->user->isLoggedIn()) {
            return $this->http->defaultRedirect('account/');
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
            $client->save();

            return $this->complete('account/');
        }
    }
}