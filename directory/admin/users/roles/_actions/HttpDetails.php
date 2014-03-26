<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\roles\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpDetails extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Details.html');
        $this->controller->fetchRole($view);

        $view['keyList'] = $view['role']->keys->fetch()
            ->orderBy('domain');
        
        return $view;
    }
}