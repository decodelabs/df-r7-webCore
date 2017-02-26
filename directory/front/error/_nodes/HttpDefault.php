<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\error\_nodes;

use df;
use df\core;
use df\arch;
use df\user;
use df\aura;
use df\link;

class HttpDefault extends arch\node\Base {

    const CHECK_ACCESS = false;
    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute() {
        if(!$exception = $this->application->getDispatchException()) {
            $this->logs->logAccessError(403, $this->request, 'You shouldn\'t be here');
            return $this->http->redirect('/');
        }

        if($exception instanceof core\IError) {
            $code = $exception->getHttpCode();
        } else {
            $code = $exception->getCode();
        }

        $lastRequest = $this->application->getDispatchRequest();

        if(!link\http\response\HeaderCollection::isValidStatusCode($code)
        || !link\http\response\HeaderCollection::isErrorStatusCode($code)) {
            $code = 500;
        }

        if($code === 401) {
            $client = $this->user->client;
            $redirectRequest = null;

            if($this->application->getRouter()->isBaseRoot()) {
                if(!$client->isLoggedIn()) {
                    $redirectRequest = arch\Request::factory('account/login');
                } else if(!$client->isConfirmed()) {
                    $redirectRequest = arch\Request::factory('account/confirm-login');
                }
            } else {
                $key = $this->data->session->stub->generateKey();
                $this->user->session->perpetuator->setJoinKey($key);
                $redirectRequest = arch\Request::factory('account/join-session?401&key='.bin2hex($key));
            }

            if($redirectRequest !== null) {
                if($lastRequest !== null) {
                    $redirectRequest->getQuery()->rt = $lastRequest->encode();
                }

                return $this->http->redirect($redirectRequest);
            }
        } else if($code == 403 && $this->user->client->isDeactivated()) {
            return $this->apex->view('Deactivated.html');
        }

        $shouldLog = true;

        if(stristr($this->http->getReferrer(), '~admin/system/error-logs')) {
            $shouldLog = false;
        }

        if($shouldLog) {
            $url = $this->http->request->getUrl();

            try {
                switch($code) {
                    case 401:
                    case 403:
                        $this->logs->logAccessError($code, $url, $exception->getMessage());
                        break;

                    case 404:
                        $this->logs->logNotFound($url, $exception->getMessage());

                        if($lastRequest && $lastRequest->isArea('admin')) {
                            $this->logs->logException($exception, $url);
                        }
                        break;

                    case 500:
                    case 502:
                    default:
                        $this->logs->logException($exception, $url);
                        break;
                }
            } catch(\Throwable $e) {
                core\debug()->exception($e);
            }
        }

        $isDevelopment = $this->application->isDevelopment();
        $isTesting = $this->application->isTesting();

        try {
            $isAdmin = $this->user->isA('developer');
        } catch(\Throwable $e) {
            $isAdmin = false;
        }

        $showTemplate = !$isDevelopment;

        if($code == 500 && ($isAdmin || $isTesting)) {
            $showTemplate = false;
        }

        if(isset($lastRequest->query->template)) {
            $showTemplate = true;
        }

        if(isset($lastRequest->query->showDump)) {
            $showTemplate = false;
        }


        $view = null;

        if($showTemplate) {
            try {
                $view = $this->apex->view($code.'.html');
            } catch(aura\view\ENotFound $e) {
                try {
                    $view = $this->apex->view('Default.html');
                } catch(aura\view\ENotFound $e) {
                    $view = null;
                }
            }
        }

        if(!$view) {
            core\debug()
                ->info('error has reached the error handler!')
                ->exception($exception)
                ->render();
        }

        if($code == 404 || $code == 500) {
            $this->application->getResponseAugmentor()->setStatusCode($code);
        }

        $view['code'] = $code;
        $view['message'] = link\http\response\HeaderCollection::statusCodeToMessage($code);

        return $view;
    }
}