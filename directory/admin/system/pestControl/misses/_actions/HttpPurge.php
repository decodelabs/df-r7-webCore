<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\pestControl\misses\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\axis;
use df\halo;

class HttpPurge extends arch\form\template\Confirm {
    
    protected function _getMainMessage($itemName) {
        return $this->_('Are you sure you want to delete all old unarchived miss logs?');
    }

    protected function _getMainButtonText() {
        return $this->_('Delete');
    }

    protected function _getMainButtonIcon() {
        return 'delete';
    }

    protected function _apply() {
        return $this->task->initiateStream('pest-control/purge-miss-logs');
    }
}