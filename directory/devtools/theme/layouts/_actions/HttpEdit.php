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
    
class HttpEdit extends HttpAdd {

    protected function _init() {
    	$config = aura\view\layout\Config::getInstance($this->application);

    	if(!$this->_layout = $config->getLayoutDefinition($this->request->query['layout'])) {
    		$this->throwError(404, 'Layout not found');
    	}
    }

    protected function _getDataId() {
    	return $this->_layout->getId();
    }

    protected function _setDefaultValues() {
    	$this->values->id = $this->_layout->getId();
    	$this->values->name = $this->_layout->getName();
    	$this->values->areas = implode(', ', $this->_layout->getAreas());
    }
}