<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\application\packages\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\spur;
    
class HttpCommit extends arch\form\Action {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    
    protected function _init() {
        
    }
}