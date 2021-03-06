<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\roles\_nodes;

use df;
use df\core;
use df\arch;

class HttpEdit extends HttpAdd {

    protected function init() {
        $this->_role = $this->scaffold->getRecord();
    }

    protected function getInstanceId() {
        return $this->_role['id'];
    }

    protected function setDefaultValues() {
        $this->values->importFrom($this->_role, [
            'name', 'signifier', 'priority'
        ]);
    }
}
