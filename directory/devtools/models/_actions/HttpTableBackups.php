<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpTableBackups extends arch\action\Base {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        $view = $this->apex->view('TableBackups.html');
        $this->controller->fetchUnit($view, 'table');

        $view['backupList'] = $view['unit']->getBackups();

        return $view;
    }
}