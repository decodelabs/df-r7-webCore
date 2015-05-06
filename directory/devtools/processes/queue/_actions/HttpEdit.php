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

class HttpEdit extends HttpAdd {
    
    protected function _init() {
        $this->_task = $this->scaffold->getRecord();
    }

    protected function _getDataId() {
        return $this->_task['id'];
    }

    protected function _setDefaultValues() {
        $this->values->importFrom($this->_task, [
            'request', 'environmentMode', 'priority'
        ]);
    }
}