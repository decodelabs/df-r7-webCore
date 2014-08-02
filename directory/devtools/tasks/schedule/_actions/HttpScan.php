<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\tasks\schedule\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\halo;
    
class HttpScan extends arch\form\template\Confirm {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'scan';

    protected function _getMainMessage($itemName) {
        return $this->_('Are you sure you want to scan for new tasks now?');
    }

    protected function _apply() {
        $task = 'manager/scan';
        return $this->task->initiateStream($task);
    }
}