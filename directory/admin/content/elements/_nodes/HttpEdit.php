<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\elements\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpEdit extends HttpAdd {

    protected function init() {
        $this->_element = $this->scaffold->getRecord();
    }

    protected function getInstanceId() {
        return $this->_element['id'];
    }

    protected function setDefaultValues() {
        $this->values->importFrom($this->_element, [
            'slug', 'name'
        ]);

        $this['body']->setSlotContent($this->_element['body']);
    }
}