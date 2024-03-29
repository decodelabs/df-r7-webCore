<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\account\_formDelegates;

use DecodeLabs\R7\Config\Users as UserConfig;
use DecodeLabs\R7\Legacy;
use df\apex;
use df\arch;
use df\core;
use df\opal;
use df\user;

abstract class RegisterBase extends arch\node\form\Delegate implements arch\node\IParentUiHandlerDelegate
{
    use arch\node\TForm_ParentUiHandlerDelegate;

    protected $_invite;

    public function setInvite(apex\models\user\invite\Record $invite = null)
    {
        $this->_invite = $invite;
        return $this;
    }

    public function getInvite()
    {
        return $this->_invite;
    }

    protected function createUi(): void
    {
        $parts = explode('\\', get_class($this));
        $name = array_pop($parts);

        $this->content->push(
            $this->apex->component('~front/account/' . $name, $this)
        );
    }

    protected function _createClient()
    {
        $client = $this->data->user->client->newRecord();
        $client->joinDate = 'now';

        return $client;
    }

    protected function _createAuth(apex\models\user\client\Record $client, $adapterName, $identity = null)
    {
        if ($identity === null) {
            $identity = $client['email'];
        }

        $auth = $this->data->user->auth->newRecord();
        $auth->user = $client;
        $auth->identity = $identity;
        $auth->adapter = $adapterName;
        $auth->bindDate = 'now';

        return $auth;
    }

    protected function _saveClient(apex\models\user\client\Record $client)
    {
        if ($this->_invite) {
            $client->groups->addList($this->_invite['#groups']);
        }

        try {
            $client->save();
        } catch (opal\rdbms\ConstraintException $e) {
            $this->values->email->addError('unique', $this->_('An account already exists with this email address'));
            $this->forceResponse(Legacy::$http->redirect($this->request));
        }

        if ($this->_invite) {
            $this->data->user->invite->claim($this->_invite, $client);
        }
    }



    public function setCompletionRedirect($request)
    {
        $this->setStore('completionRedirect', $request);
        return $this;
    }

    public function getCompletionRedirect()
    {
        return $this->getStore('completionRedirect');
    }

    public function clearCompletionRedirect()
    {
        $this->removeStore('completionRedirect');
        return $this;
    }

    protected function _completeRegistration($requestGenerator = null)
    {
        return $this->complete(function () use ($requestGenerator) {
            $redirect = $this->getStore('completionRedirect');
            $config = UserConfig::load();

            if ($redirect === null) {
                $redirect = $config->getRegistrationLandingPage();
            }

            $this->comms->flashSuccess(
                'registration.complete',
                $this->_('Your account has been successfully created')
            );

            if (
                $requestGenerator &&
                $config->shouldLoginOnRegistration()
            ) {
                $request = core\lang\Callback($requestGenerator);

                if ($request instanceof user\authentication\IRequest) {
                    $result = $this->user->auth->bind($request);
                    return $redirect;
                }
            }

            $request = $this->uri->directoryRequest('account/login');
            $request->setRedirect($this->request->getRedirectFrom(), $redirect);

            return $request;
        });
    }
}
