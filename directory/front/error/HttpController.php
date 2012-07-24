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

class HttpController extends arch\Controller {
    
    const CHECK_ACCESS = false;
    const DEFAULT_ACCESS = user\IState::ALL;
    
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
        
        core\debug()
            ->info('error has reached the error handler!')
            ->exception($exception)
            ->flush();
        
    }
}