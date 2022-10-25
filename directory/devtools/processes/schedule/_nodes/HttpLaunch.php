<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\processes\schedule\_nodes;

use df\arch;

class HttpLaunch extends arch\node\ConfirmForm
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected $_schedule;

    protected function init(): void
    {
        $this->_schedule = $this->scaffold->getRecord();
    }

    protected function getInstanceId(): ?string
    {
        return $this->_schedule['id'];
    }

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to launch this task now?');
    }

    protected function createItemUi($container)
    {
        $container->push(
            $this->apex->component('ScheduleDetails')
                ->setRecord($this->_schedule)
        );
    }

    protected function apply()
    {
        $this->_schedule->lastRun = 'now';
        $this->_schedule->save();
        return $this->task->initiateStream($this->_schedule['request']);
    }
}
