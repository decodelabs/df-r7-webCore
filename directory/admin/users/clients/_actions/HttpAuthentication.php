<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\clients\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpAuthentication extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Authentication.html');
        $this->controller->fetchClient($view);

        $view['authenticationList'] = $view['client']->authDomains->fetch()
            ->orderBy('adapter ASC');

        return $view;
    }
}