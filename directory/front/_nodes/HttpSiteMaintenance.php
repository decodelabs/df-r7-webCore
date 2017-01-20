<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpSiteMaintenance extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute() {
        if(!df\Launchpad::$isMaintenance) {
            return $this->http->defaultRedirect();
        }

        $view = $this->apex->view('SiteMaintenance.html')
            ->setLayout(null);

        $view->getHeaders()->setStatusCode(503);
        return $view;
    }
}