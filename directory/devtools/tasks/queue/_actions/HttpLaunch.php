<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\tasks\queue\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\halo;
    
class HttpLaunch extends arch\form\template\Confirm {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'task';

    protected $_task;

    protected function _init() {
        $this->_task = $this->data->fetchForAction(
            'axis://task/Queue',
            $this->request->query['task']
        );
    }

    protected function _getMainMessage($itemName) {
        return $this->_('Are you sure you want to launch this task now?');
    }

    protected function _renderItemDetails($container) {
        $container->push($this->import->component('~devtools/tasks/queue/TaskDetails')->setRecord($this->_task));
    }

    protected function _apply() {
        $task = 'manager/launch-queued?id='.$this->_task['id'];
        return $this->task->initiateStream($task);
    }
}