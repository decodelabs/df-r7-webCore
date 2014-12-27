<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\application\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        $view = $this->apex->newWidgetView();
        $view->content->push($this->apex->component('~devtools/application/IndexHeaderBar'));
        $view->content->addBlockMenu('directory://~devtools/application/Index');

        return $view;
    }
}