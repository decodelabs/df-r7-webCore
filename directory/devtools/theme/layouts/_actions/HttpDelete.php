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
    
class HttpDelete extends arch\form\template\Delete {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'layout';

    protected $_layout;

    protected function _init() {
    	$config = aura\view\layout\Config::getInstance($this->application);

    	if(!$this->_layout = $config->getLayoutDefinition($this->request->query['layout'])) {
    		$this->throwError(404, 'Layout not found');
    	}
    }

    protected function _getDataId() {
    	return $this->_layout->getId();
    }

    protected function _renderItemDetails(aura\html\widget\IContainerWidget $container) {
		$container->push(
			$this->html->attributeList($this->_layout)
				// Id
				->addField('id', function($layout) {
					return $layout->getId();
				})

				// Name
				->addField('name', function($layout) {
					return $layout->getName();
				})

				// Slots
				->addField('slots', function($layout) {
					return $layout->countSlots();
				})
		);
	}

	protected function _deleteItem() {
		$config = aura\view\layout\Config::getInstance($this->application);
		$config->removeLayoutDefinition($this->_layout)->save();
	}
}