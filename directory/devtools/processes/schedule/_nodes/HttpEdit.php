<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\schedule\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpEdit extends HttpAdd {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected function init() {
        $this->_schedule = $this->scaffold->getRecord();
    }

    protected function getInstanceId() {
        return $this->_schedule['id'];
    }

    protected function setDefaultValues() {
        $this->values->importFrom($this->_schedule, [
            'request', 'minute', 'hour',
            'day', 'month', 'weekday', 'isLive'
        ]);
    }
}