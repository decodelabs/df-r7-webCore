<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\media\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpEdit extends HttpAdd {

    protected function init() {
        $this->_bucket = $this->scaffold->getRecord();
    }

    protected function getInstanceId() {
        return $this->_bucket['id'];
    }

    protected function setDefaultValues() {
        $this->values->importFrom($this->_bucket, [
            'name', 'slug', 'context1', 'context2'
        ]);
    }
}