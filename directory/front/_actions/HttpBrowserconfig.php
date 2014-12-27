<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\neon;

class HttpBrowserconfig extends arch\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::ALL;
    const CHECK_ACCESS = false;

    public function executeAsXml() {
        $view = $this->apex->view('Browserconfig.xml');
        $theme = $this->apex->getTheme();

        $view['hasImage'] = (bool)$theme->getApplicationImagePath();
        $view['tileColor'] = $theme->getApplicationColor()->toHexString();

        return $view;
    }
}