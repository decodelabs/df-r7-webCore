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
use df\axis;
use df\halo;

class HttpBackup extends arch\form\template\Confirm {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected function _getMainMessage($itemName) {
        return $this->_('Are you sure you want to back up all data? This process may take a while!');
    }

    protected function _getMainButtonText() {
        return $this->_('Back up');
    }

    protected function _getMainButtonIcon() {
        return 'backup';
    }

    protected function _apply() {
        $task = 'axis/backup';
        return $this->task->initiateStream($task);
    }
}