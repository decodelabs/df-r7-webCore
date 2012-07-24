<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_actions;

use df;
use df\core;
use df\apex;
use df\user;
use df\arch;
    
class HttpIndex extends arch\Action {

    const CHECK_ACCESS = false;
    const DEFAULT_ACCESS = user\IState::BOUND;

    public function execute() {
    	$client = $this->user->client;

    	if(!$client->isLoggedIn()) {
    		// throw 401

    		return $this->http->redirect('account/login');
    	}

    	$view = $this->aura->getView('Index.html');
    	$view['client'] = $client;

    	return $view;
    }
}