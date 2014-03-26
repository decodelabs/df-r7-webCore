<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\deactivations\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpDetails extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Details.html');

        $view['deactivation'] = $this->data->fetchForAction(
            'axis://user/ClientDeactivation',
            $this->request->query['deactivation']
        );

        return $view;
    }
}