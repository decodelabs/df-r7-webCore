<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\notFound\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpDetails extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Details.html');

        $view['error'] = $this->data->fetchForAction(
            'axis://log/NotFound',
            $this->request->query['error']
        );

        return $view;
    }
}