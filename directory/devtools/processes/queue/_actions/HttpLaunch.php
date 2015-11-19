<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\queue\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\halo;

class HttpLaunch extends arch\action\ConfirmForm {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'task';

    protected $_task;

    protected function init() {
        $this->_task = $this->scaffold->getRecord();
    }

    protected function getMainMessage() {
        return $this->_('Are you sure you want to launch this task now?');
    }

    protected function createItemUi($container) {
        $container->push($this->apex->component('~devtools/processes/queue/TaskDetails')->setRecord($this->_task));
    }

    protected function apply() {
        $task = 'tasks/launch-queued?id='.$this->_task['id'];
        return $this->task->initiateStream($task);
    }
}