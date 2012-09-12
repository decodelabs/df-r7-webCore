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
    
class HttpDeleteSlot extends arch\form\template\Delete {

	const DEFAULT_ACCESS = arch\IAccess::DEV;
	const ITEM_NAME = 'slot';

	protected $_layout;
	protected $_slot;

	protected function _init() {
		$config = aura\view\layout\Config::getInstance();

		if(!$this->_layout = $config->getLayoutDefinition($this->request->query['layout'])) {
			$this->throwError(404, 'Layout not found');
		}

		if(!$this->_slot = $this->_layout->getSlot($this->request->query['slot'])) {
			$this->throwError(404, 'Slot not found');
		}
	}

	protected function _getDataId() {
		return $this->_layout->getId().':'.$this->_slot->getId();
	}

	protected function _renderItemDetails(aura\html\widget\IContainerWidget $container) {
		$container->push(
			$this->html->attributeList($this->_slot)
				// Id
				->addField('id', function($slot) {
					return $slot->getId();
				})

				// Name
				->addField('name', function($slot) {
					return $slot->getName();
				})
		);
	}

	protected function _deleteItem() {
		$config = aura\view\layout\Config::getInstance($this->application);
		$this->_layout->removeSlot($this->_slot);

		$config->setLayoutDefinition($this->_layout)->save();
	}
}