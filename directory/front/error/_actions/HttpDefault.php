<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\error\_actions;

use df;
use df\core;
use df\arch;
use df\user;
use df\aura;
use df\link;

class HttpDefault extends arch\Action {
    
    const CHECK_ACCESS = false;
    const DEFAULT_ACCESS = arch\IAccess::ALL;
    
    public function execute() {
        $request = $this->_context->request;
        
        if(!$request instanceof arch\IErrorRequest) {
            $this->throwError(404, 'You shouldn\'t be here');
        }
        
        $exception = $request->getException();
        $code = $exception->getCode();
        $lastRequest = $request->getLastRequest();

        if(!link\http\response\HeaderCollection::isValidStatusCode($code)
        || !link\http\response\HeaderCollection::isErrorStatusCode($code)) {
            $code = 500;
        }
        
        if($code === 401) {
            $client = $this->user->client;
            $redirectRequest = null;
            
            if(!$client->isLoggedIn()) {
                $redirectRequest = arch\Request::factory('account/login');
            } else if(!$client->isConfirmed()) {
                $redirectRequest = arch\Request::factory('account/confirm-login');
            }
            
            if($redirectRequest !== null) {
                if($lastRequest !== null) {
                    $redirectRequest->getQuery()->rt = $lastRequest->encode();
                }
                
                return $this->http->redirect($redirectRequest)->isTemporary(true);
            }
        } else if($code == 403 && $this->user->client->isDeactivated()) {
            return $this->aura->getView('Deactivated.html');
        }

        $shouldLog = true;

        if(stristr($this->http->getReferrer(), '~admin/system/error-logs')) {
            $shouldLog = false;
        }

        if($shouldLog) {
            try {
                switch($code) {
                    case 401:
                    case 403:
                        $this->logs->logAccessError($code, $lastRequest, $exception->getMessage());
                        break;

                    case 404:
                        $this->logs->logNotFound($lastRequest, $exception->getMessage());
                        break;

                    case 500:
                    case 502:
                        $this->logs->logException($exception, $lastRequest->toString());
                        break;
                }
            } catch(\Exception $e) {
                core\debug()->exception($e);
            }
        }
        
        $isDevelopment = $this->application->isDevelopment();
        $isDevelopment = $this->application->isTesting();

        try {
            $isAdmin = $this->user->canAccess('virtual://errors');
        } catch(\Exception $e) {
            $isAdmin = false;
        }

        $showTemplate = !$isDevelopment || isset($lastRequest->query->template);

        if($code == 500 && ($isAdmin || $isTesting)) {
            $showTemplate = false;
        }

        if(isset($lastRequest->query->showErrorTemplate)) {
            $showTemplate = true;
        }

        if(isset($lastRequest->query->showDump)) {
            $showTemplate = false;
        }


        $view = null;

        if($showTemplate) {
            try {
                $view = $this->aura->getView($code.'.html');
            } catch(aura\view\ContentNotFoundException $e) {
                try {
                    $view = $this->aura->getView('Default.html');
                } catch(aura\view\ContentNotFoundException $e) {
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