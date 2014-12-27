<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\capture\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpMessage extends arch\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        $view = $this->apex->view('Message.html');

        $view['mail'] = $this->data->fetchForAction(
            'axis://mail/Capture',
            $this->request->query['mail']
        );

        $view['message'] = $view['mail']->toMessage();
        $view->setLayout('Blank');
        
        return $view;
    }
}