<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front;

use df;
use df\core;
use df\arch;
use df\aura;
use df\user;

class HttpController extends arch\Controller {
    
	const CHECK_ACCESS = false;
	const DEFAULT_ACCESS = user\IState::ALL;

    public function indexHtmlAction() {
        $view = $this->aura->getView('Index.html');
        
        return $view;
    }

    public function crossdomainXmlAction() {
    	return $this->aura->getView('Crossdomain.xml');
    }
}
