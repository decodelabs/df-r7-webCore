<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\auth0\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\user;

use DecodeLabs\Exceptional;

class HttpLogin extends arch\node\Base
{
    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute()
    {
        $link = $this->user->isLoggedIn();
        $config = user\authentication\Config::getInstance();

        if (!$config->isAdapterEnabled('Auth0')) {
            throw Exceptional::Forbidden([
                'message' => 'Auth0 is not enabled',
                'http' => 403
            ]);
        }

        $_GET = $this->request->query->toArray();

        if ($link) {
            $adapter = $this->user->auth->loadAdapter('Auth0');

            $adapter->authenticate(
                $this->user->auth->newRequest('Auth0')
                    ->setIdentity($this->user->getId())
                    ->setAttribute('redirect', $this->request),
                $result = new user\authentication\Result('Auth0')
            );
        } else {
            $result = $this->user->auth->bind(
                $this->user->auth->newRequest('Auth0')
                    ->setAttribute('redirect', $this->request)
            );
        }

        if (!$success = $result->isValid()) {
            if ($result->getCode() === $result::NO_STATUS) {
                $this->comms->flashError('This account is currently disabled');
            } else {
                $this->comms->flashError('Unable to authenticate with Auth0');
            }

            if (!$link) {
                return $this->http->redirect(
                    $this->uri->directoryRequest('account/login')
                        ->setRedirectFrom($this->request->getRedirectFrom())
                        ->setRedirectTo($this->request->getRedirectTo())
                );
            }
        } else {
            if ($link) {
                $this->comms->flashSuccess('Your account has been successfully linked');
            }
        }

        return $this->http->defaultRedirect('/', $success);
    }
}
