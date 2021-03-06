<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\queue\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\halo;

class HttpSpool extends arch\node\ConfirmForm {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'spool';

    protected function getMainMessage() {
        return $this->_('Are you sure you want to run the task queue spool now?');
    }

    protected function apply() {
        return $this->task->initiateStream('tasks/spool');
    }
}