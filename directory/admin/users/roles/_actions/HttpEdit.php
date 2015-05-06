<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\roles\_actions;

use df;
use df\core;
use df\arch;

class HttpEdit extends HttpAdd {
    
    protected function _init() {
        $this->_role = $this->scaffold->getRecord();
    }

    protected function _getDataId() {
        return $this->_role['id'];
    }
    
    protected function _setDefaultValues() {
        $this->values->importFrom($this->_role, [
            'name', 'bindState', 'minRequiredState', 'priority'
        ]);
    }
}
