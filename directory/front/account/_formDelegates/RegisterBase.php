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

abstract class RegisterBase extends arch\form\Delegate implements arch\form\IParentUiHandlerDelegate {
    
    protected $_invite;

    public function setInvite(apex\models\user\invite\Record $invite=null) {
        $this->_invite = $invite;
        return $this;
    }

    public function getInvite() {
        return $this->_invite;
    }
    
    public function renderUi() {
        $parts = explode('\\', get_class($this));
        $name = array_pop($parts);

        $this->content->push(
            $this->import->component('~front/account/'.$name, $this)
        );
    }

    protected function _createClient() {
        $client = $this->data->user->client->newRecord();
        $client->joinDate = 'now';

        return $client;
    }

    protected function _createAuth(apex\models\user\client\Record $client, $adapterName, $identity=null) {
        if($identity === null) {
            $identity = $client['email'];
        }

        $auth = $this->data->user->auth->newRecord();
        $auth->user = $client;
        $auth->identity = $identity;
        $auth->adapter = $adapterName;
        $auth->bindDate = 'now';

        return $auth;
    }

    protected function _saveClient(apex\models\user\client\Record $client) {
        if($this->_invite) {
            $client->groups->addList($this->_invite->groups->getRelatedPrimaryKeys());
        }

        $client->save();

        if($this->_invite) {
            $this->data->user->invite->claim($this->_invite, $client);
        }
    }

    protected function _completeRegistration(Callable $requestGenerator=null) {
        $config = $this->data->user->config;

        if($requestGenerator && $config->shouldLoginOnRegistration()) {
            $request = $requestGenerator();

            if($request instanceof user\authentication\IRequest) {
                $result = $this->user->authenticate($request);
                return $this->complete($config->getRegistrationLandingPage());
            }
        }

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
