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

class HttpDetails extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Details.html');
        $this->controller->fetchClient($view);

        $view['emailList'] = $this->data->user->emailVerify->fetchEmailList($view['client']);

        return $view;
    }
}