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
use df\fire;

class HttpIndex extends arch\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        $config = fire\Config::getInstance();
        $view = $this->apex->view('Index.html');

        $view['layoutList'] = $config->getAllLayoutDefinitions();

        return $view;
    }
}