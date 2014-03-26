<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\groups\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpDetails extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Details.html');
        $this->controller->fetchGroup($view);
        
        $view['roleList'] = $view['group']->roles->fetch()
            ->countRelation('groups')
            ->countRelation('keys')
            ->orderBy('priority');

        return $view;
    }
}