<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpIndex extends arch\Action {

    const DEFAULT_ACCESS = arch\IAccess::CONFIRMED;

    public function execute() {
        return $this->apex->view('Index.html');
    }
}