<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\error;

use df;
use df\core;
use df\arch;
use df\user;
use df\aura;
use df\halo;

class HttpController extends arch\Controller {
    
    const CHECK_ACCESS = false;
    const DEFAULT_ACCESS = arch\IAccess::ALL;
    
    public function defaultAction() {
        $request = $this->_context->request;
        
        if(!$request instanceof arch\IErrorRequest) {
            $this->throwError(404, 'You shouldn\'t be here');
        }
        
        $exception = $request->getException();
        $code = $exception->getCode();
        $lastRequest = $request->getLastRequest();

        if(!halo\protocol\http\response\HeaderCollection::isValidStatusCode($code)
        || !halo\protocol\http\response\HeaderCollection::isErrorStatusCode($code)) {
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
        }

        try {
            $this->data->error->log->newRecord([
                    'code' => $code,
                    'mode' => $this->getRunMode(),
                    'request' => $lastRequest->toString(),
                    'message' => $exception->getMessage(),
                    'user' => $this->user->isLoggedIn() ? $this->user->client->getId() : null,
                    'production' => $this->application->isProduction()
                ])
                ->save();
        } catch(\Exception $e) {
            core\debug()->exception($e);
        }
        
        $isDevelopment = $this->application->isDevelopment();

        try {
            $isAdmin = $this->user->canAccess('virtual://errors');
        } catch(\Exception $e) {
            $isAdmin = false;
        }

        $showTemplate = !$isDevelopment;

        if($code == 500 && $isAdmin) {
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

        $view['code'] = $code;
        $view['message'] = halo\protocol\http\response\HeaderCollection::statusCodeToMessage($code);

        return $view;
    }
}