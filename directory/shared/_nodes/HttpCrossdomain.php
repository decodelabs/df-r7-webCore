<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\shared\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpCrossdomain extends arch\node\Base {

    const OPTIMIZE = true;
    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executeAsXml() {
        return $this->apex->view('Crossdomain.xml');
    }
}
