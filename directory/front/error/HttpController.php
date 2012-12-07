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
        $request = $this->_context->getRequest();
        
        if(!$request instanceof arch\IErrorRequest) {
            $this->throwError(404, 'You shouldn\'t be here');
        }
        
        $exception = $request->getException();
        $code = $exception->getCode();
        $lastRequest = $request->getLastRequest();
        
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
        
        $isDevelopment = $this->application->isDevelopment();
        $view = null;

        if((!$isDevelopment || isset($lastRequest->query->showErrorTemplate))
        && (!isset($lastRequest->query->showDump))) {
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
                ->flush();
        }

        if(!halo\protocol\http\response\HeaderCollection::isValidStatusCode($code)) {
            $code = 500;
        }
        
        $view['code'] = $code;
        $view['message'] = halo\protocol\http\response\HeaderCollection::statusCodeToMessage($code);

        return $view;
    }
}