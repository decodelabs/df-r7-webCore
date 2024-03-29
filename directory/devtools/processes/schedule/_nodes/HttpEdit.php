<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\processes\schedule\_nodes;

use df\arch;

class HttpEdit extends HttpAdd
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected function init(): void
    {
        $this->_schedule = $this->scaffold->getRecord();
    }

    protected function getInstanceId(): ?string
    {
        return $this->_schedule['id'];
    }

    protected function setDefaultValues(): void
    {
        $this->values->importFrom($this->_schedule, [
            'request', 'minute', 'hour',
            'day', 'month', 'weekday', 'isLive'
        ]);
    }
}
