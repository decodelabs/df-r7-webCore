<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\_nodes;

use df;
use df\core;
use df\arch;

class HttpIndex extends arch\node\Base {

    const CHECK_ACCESS = true;
    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executeAsHtml() {
        return $this->apex->view('Index.html');
    }
}
