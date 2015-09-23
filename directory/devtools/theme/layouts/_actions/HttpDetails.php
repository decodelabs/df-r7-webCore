<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\theme\layouts\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpDetails extends arch\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        $view = $this->apex->view('Details.html');
        $this->controller->fetchLayout($view);

        return $view;
    }
}