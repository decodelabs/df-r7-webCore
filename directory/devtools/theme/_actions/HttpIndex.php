<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\theme\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\action\Base {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        $view = $this->apex->newWidgetView();
        $view->content->push($this->apex->component('~devtools/theme/IndexHeaderBar'));
        $view->content->addBlockMenu('directory://~devtools/theme/Index');

        return $view;
    }
}