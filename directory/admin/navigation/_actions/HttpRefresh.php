<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\navigation\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpRefresh extends arch\Action {
    
    public function executeAsHtml() {
        $this->apex->clearMenuCache();

        $this->comms->flash(
            'menu-cache.clear', 
            $this->_('The system menu list has been refreshed'), 
            'success'
        );

        return $this->http->defaultRedirect();
    }
}