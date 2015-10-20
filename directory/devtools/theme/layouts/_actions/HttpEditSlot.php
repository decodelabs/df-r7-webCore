<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\theme\layouts\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;
use df\fire;

class HttpEditSlot extends HttpAddSlot {

    protected function init() {
        $config = fire\Config::getInstance();

        if(!$this->_layout = $config->getLayoutDefinition($this->request['layout'])) {
            $this->throwError(404);
        }

        if(!$this->_slot = $this->_layout->getSlot($this->request['slot'])) {
            $this->throwError(404, 'Slot not found');
        }
    }

    protected function setDefaultValues() {
        $this->values->id = $this->_slot->getId();
        $this->values->name = $this->_slot->getName();
        $this->values->minBlocks = $this->_slot->getMinBlocks();
        $this->values->maxBlocks = $this->_slot->getMaxBlocks();
        $this->values->category = $this->_slot->getCategory();
    }
}