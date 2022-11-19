<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\shared\_nodes;

use df\arch;

class HttpRobots extends arch\node\Base
{
    public const OPTIMIZE = true;
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executeAsTxt()
    {
        return $this->apex->view('Robots.txt');
    }
}
