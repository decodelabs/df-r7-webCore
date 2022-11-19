<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\queue\_nodes;

use df\arch;

class HttpSpool extends arch\node\ConfirmForm
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const ITEM_NAME = 'spool';

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to run the task queue spool now?');
    }

    protected function apply()
    {
        return $this->task->initiateStream('tasks/spool');
    }
}
