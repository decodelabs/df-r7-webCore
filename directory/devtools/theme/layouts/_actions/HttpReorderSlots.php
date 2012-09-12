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
    
class HttpReorderSlots extends arch\form\Action {

	const DEFAULT_ACCESS = arch\IAccess::DEV;
	const DEFAULT_EVENT = 'save';

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

    protected function _setDefaultValues() {
    	$i = 1;

    	foreach($this->_layout->getSlots() as $slot) {
    		$this->values->slots[] = [
    			'id' => $slot->getId(),
    			'weight' => $i
			];

			$i++;
    	}
    }

    protected function _createUi() {
    	$form = $this->content->addForm();
    	$fs = $form->addFieldSet($this->_('Slot order'));
    	$fa = $fs->addFieldArea();

    	foreach($this->values->slots as $i => $slotNode) {
    		if(!$slot = $this->_layout->getSlot($slotNode['id'])) {
    			continue;
    		}

			$fa->push(
				$this->html->element('div', [
	    			$this->html->numberTextbox('slots['.$i.'][weight]', $slotNode->weight)
	    				->setStep(1)
	    				->isRequired(true),

					$this->html->hidden('slots['.$i.'][id]', $slot->getId()),

					$slot->getName()
				])
			);
    	}

    	$fa->push(
    		$this->html->eventButton($this->eventName('refresh'), $this->_('Update form'))
    			->setIcon('refresh')
    			->setDisposition('informative')
		);

    	$fs->push($this->html->defaultButtonGroup());
    }

    protected function _onRefreshEvent() {
    	$queue = new \SplPriorityQueue();

    	foreach($this->values->slots as $slotNode) {
    		$queue->insert($slotNode['id'], $slotNode->get('weight', 10000));
    	}

    	$this->values->remove('slots');
    	$i = count($queue);

    	foreach($queue as $id) {
    		if(!$slot = $this->_layout->getSlot($id)) {
    			continue;
    		}

			$this->values->slots->unshift([
    			'id' => $slot->getId(),
    			'weight' => $i
			]);

			$i--;
    	}
    }

    protected function _onSaveEvent() {
    	$this->_onRefreshEvent();
    	$ids = array();

    	foreach($this->values->slots as $slotNode) {
    		$ids[] = $slotNode['id'];
    	}

    	$this->_layout->setSlotOrder($ids);

    	if($this->isValid()) {
    		$config = aura\view\layout\Config::getInstance($this->application);
    		$config->setLayoutDefinition($this->_layout)->save();

    		$this->arch->notify(
    			'slot.reorder',
    			$this->_('The layout slots have been successfully reordered'),
    			'success'
			);

			return $this->complete();
    	}
    }
}