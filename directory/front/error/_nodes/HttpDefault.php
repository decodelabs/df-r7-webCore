<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\error\_nodes;

use DecodeLabs\Disciple;
use DecodeLabs\Exceptional;
use DecodeLabs\Genesis;

use DecodeLabs\Glitch;
use DecodeLabs\R7\Legacy;
use df\arch;
use df\aura;
use df\link;

class HttpDefault extends arch\node\Base
{
    public const CHECK_ACCESS = false;
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute()
    {
        if (!$exception = Legacy::$http->getDispatchException()) {
            if (Genesis::$environment->isDevelopment()) {
                $code = $this->request->getNode();

                if (!link\http\response\HeaderCollection::isValidStatusCode($code)) {
                    $code = 403;
                }

                $exception = new \Exception('Testing...', $code);
            } else {
                $this->logs->logAccessError(403, $this->request, 'You shouldn\'t be here');
                return Legacy::$http->redirect('/');
            }
        }

        if ($exception instanceof Exceptional\Exception) {
            $code = $exception->getHttpStatus();
        } else {
            $code = $exception->getCode();
        }

        $lastRequest = Legacy::$http->getDispatchRequest();

        if (
            !link\http\response\HeaderCollection::isValidStatusCode($code) ||
            !link\http\response\HeaderCollection::isErrorStatusCode($code)
        ) {
            $code = 500;
        }

        // Ensure session is open in case a widget tries to open it while rendering error page
        $this->user->isLoggedIn();

        if ($code === 401) {
            $redirectRequest = null;

            if (Legacy::$http->getRouter()->isBaseRoot()) {
                if (!Disciple::isLoggedIn()) {
                    $redirectRequest = arch\Request::factory('account/login');
                } elseif (!$this->user->client->isConfirmed()) {
                    $redirectRequest = arch\Request::factory('account/confirm-login');
                }
            } else {
                $key = $this->data->session->stub->generateKey();
                $this->user->session->perpetuator->setJoinKey($key);
                $redirectRequest = arch\Request::factory('account/join-session?401&key=' . bin2hex($key));
            }

            if ($redirectRequest !== null) {
                if ($lastRequest !== null) {
                    $redirectRequest->getQuery()->rt = $lastRequest->encode();
                }

                return Legacy::$http->redirect($redirectRequest);
            }
        } elseif ($code == 403 && $this->user->client->isDeactivated()) {
            return $this->apex->view('Deactivated.html');
        }


        $isDevelopment = Genesis::$environment->isDevelopment();
        $isTesting = Genesis::$environment->isTesting();

        try {
            $isAdmin = $this->user->isA('developer');
        } catch (\Throwable $e) {
            $isAdmin = false;
        }

        if ($isAdmin) {
            Glitch::getRenderer()->setProductionOverride(true);
        }

        $showTemplate = !$isDevelopment;

        if ($code == 500 && ($isAdmin || $isTesting)) {
            $showTemplate = false;
        }

        if (isset($lastRequest->query->template)) {
            $showTemplate = true;
        }

        if (isset($lastRequest->query->showDump)) {
            $showTemplate = false;
        }


        $view = null;

        if ($showTemplate) {
            try {
                $view = $this->apex->view($code . '.html');
            } catch (aura\view\NotFoundException $e) {
                try {
                    $view = $this->apex->view('Default.html');
                } catch (aura\view\NotFoundException $e) {
                    $view = null;
                }
            }
        }

        if (!$view) {
            Glitch::dumpException($exception);
        }

        if ($code == 403 || $code == 404 || $code == 500) {
            Legacy::$http->getResponseAugmentor()->setStatusCode($code);
        }

        $view['code'] = $code;
        $view['message'] = link\http\response\HeaderCollection::statusCodeToMessage($code);

        return $view;
    }
}
