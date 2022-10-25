<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\processes\queue\_nodes;

class HttpEdit extends HttpAdd
{
    protected function init(): void
    {
        $this->_task = $this->scaffold->getRecord();
    }

    protected function getInstanceId(): ?string
    {
        return $this->_task['id'];
    }

    protected function setDefaultValues(): void
    {
        $this->values->importFrom($this->_task, [
            'request', 'priority'
        ]);
    }
}
