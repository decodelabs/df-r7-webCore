<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    
    public function executeAsHtml() {
        $view = $this->aura->getWidgetContainer();
        $view->addBlockMenu('directory://~devtools/Index');

        return $view;
    }
}