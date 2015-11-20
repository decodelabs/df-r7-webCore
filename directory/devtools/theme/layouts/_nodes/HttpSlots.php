<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\theme\layouts\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpSlots extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        $view = $this->apex->view('Slots.html');
        $this->controller->fetchLayout($view);

        $view['slotList'] = $view['layout']->getSlots();
        return $view;
    }
}