<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\schedule\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpEdit extends HttpAdd {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected function _init() {
        $this->_schedule = $this->scaffold->getRecord();
    }

    protected function _getDataId() {
        return $this->_schedule['id'];
    }

    protected function _setDefaultValues() {
        $this->values->importFrom($this->_schedule, [
            'request', 'environmentMode', 'minute', 'hour',
            'day', 'month', 'weekday', 'isLive'
        ]);

        if(empty($this->values['environmentMode'])) {
            $this->values->environmentMode = '';
        }
    }
}