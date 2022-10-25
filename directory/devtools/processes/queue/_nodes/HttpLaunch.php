<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\processes\queue\_nodes;

use df\arch;

class HttpLaunch extends arch\node\ConfirmForm
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const ITEM_NAME = 'task';

    protected $_task;

    protected function init(): void
    {
        $this->_task = $this->scaffold->getRecord();
    }

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to launch this task now?');
    }

    protected function createItemUi($container)
    {
        $container->push($this->apex->component('~devtools/processes/queue/TaskDetails')->setRecord($this->_task));
    }

    protected function apply()
    {
        return $this->task->initiateStream('tasks/launch-queued?id='.$this->_task['id']);
    }
}
