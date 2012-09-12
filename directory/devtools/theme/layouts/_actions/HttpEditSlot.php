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
    
class HttpEditSlot extends HttpAddSlot {

    protected function _init() {
    	$config = aura\view\layout\Config::getInstance($this->application);

    	if(!$this->_layout = $config->getLayoutDefinition($this->request->query['layout'])) {
    		$this->throwError(404);
    	}

    	if(!$this->_slot = $this->_layout->getSlot($this->request->query['slot'])) {
    		$this->throwError(404, 'Slot not found');
    	}
    }

    protected function _setDefaultValues() {
    	$this->values->id = $this->_slot->getId();
    	$this->values->name = $this->_slot->getName();
    	$this->values->minBlocks = $this->_slot->getMinBlocks();
    	$this->values->maxBlocks = $this->_slot->getMaxBlocks();
    	$this->values->blockTypes = $this->_slot->getBlockTypes();
    }
}