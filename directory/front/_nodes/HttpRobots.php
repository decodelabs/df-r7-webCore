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

class HttpRobots extends arch\node\Base {

    const OPTIMIZE = true;
    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executeAsTxt() {
        return $this->apex->view('Robots.txt');
    }
}