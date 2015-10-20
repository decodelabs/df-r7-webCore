<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\media\files\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpEdit extends HttpAdd {

    protected function init() {
        $this->_file = $this->scaffold->getRecord();
    }

    protected function getInstanceId() {
        return $this->_file['id'];
    }

    protected function setDefaultValues() {
        $this->values->importFrom($this->_file, [
            'slug', 'fileName'
        ]);

        $this['bucket']->setSelected($this->_file['#bucket']);
        $this['owner']->setSelected($this->_file['#owner']);

        $this->values->overwriteName = true;
    }
}