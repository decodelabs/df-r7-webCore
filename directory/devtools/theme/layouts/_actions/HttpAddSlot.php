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
    
class HttpAddSlot extends arch\form\Action {

	const DEFAULT_ACCESS = arch\IAccess::DEV;
	const DEFAULT_EVENT = 'save';

    protected $_layout;
    protected $_slot;

    protected function _init() {
    	$config = aura\view\layout\Config::getInstance($this->application);

    	if(!$this->_layout = $config->getLayoutDefinition($this->request->query['layout'])) {
    		$this->throwError(404);
    	}

    	$this->_slot = new aura\view\layout\SlotDefinition('__default__');
    }

    protected function _getDataId() {
    	return $this->_layout->getId().':'.$this->_slot->getId();
    }

    protected function _createUi() {
    	$form = $this->content->addForm();
    	$detailsFs = $form->addFieldSet($this->_('Slot details'));

    	// Layout
    	$detailsFs->addFieldArea($this->_('Layout'))->push(
    		$this->html->textbox('layout', $this->_layout->getName())
    			->isDisabled(true)
		);

		// Id
		$detailsFs->addFieldArea($this->_('Id'))->push(
			$this->html->textbox('id', $this->values->id)
				->isRequired(true)
				->isReadOnly($this->_slot->isPrimary())
		);

		// Name
		$detailsFs->addFieldArea($this->_('Name'))->push(
			$this->html->textbox('name', $this->values->name)
				->isRequired(true)
		);

		// Min blocks
		$detailsFs->addFieldArea($this->_('Min blocks'))->push(
			$this->html->numberTextbox('minBlocks', $this->values->minBlocks)
				->setStep(1)
				->setMin(0)
		);

		// Max blocks
		$detailsFs->addFieldArea($this->_('Max blocks'))->push(
			$this->html->numberTextbox('maxBlocks', $this->values->maxBlocks)
				->setStep(1)
				->setMin(1)
		);


		// Block types
		$detailsFs->addFieldArea($this->_('Block types'))->push(
			// TODO: add block type selector
			$this->html->notification('Block type selector coming soon...', 'debug')
		);

		// Buttons
		$detailsFs->push($this->html->defaultButtonGroup());
    }


    protected function _onSaveEvent() {
    	$this->data->newValidator()
    		->shouldSanitize(true)

    		// Id
    		->addField('id', 'text')
    			->setPattern('/^[a-zA-Z0-9]+$/')
    			->isRequired(true)
    			->end()

			// Name
			->addField('name', 'text')
				->isRequired(true)
				->end()

			// Min blocks
			->addField('minBlocks', 'integer')
				->setMin(0)
				->end()

			// Max blocks
			->addField('maxBlocks', 'integer')
				->setMin(1)
				->end()

			// Block types
			//->addField('blockTypes')

			->validate($this->values)
				;


		$config = aura\view\layout\Config::getInstance();

		if($this->isValid()) {
			if($this->values['id'] !== $this->_slot->getId()) {
				if($this->_layout->getSlot($this->values['id'])) {
					$this->values->id->addError('unique', $this->_(
						'There is already a slot with that id'
					));
				} else if($this->_slot->isPrimary()) {
					$this->values->id->addError('static', $this->_(
						'The primary slot is required so you cannot change this id'
					));
				}
			}
		}

		if($this->isValid()) {
			$this->_layout->removeSlot($this->_slot->getId());

			$this->_slot->setId($this->values['id'])
				->setName($this->values['name'])
				->setMinBlocks($this->values['minBlocks'])
				->setMaxBlocks($this->values['maxBlocks'])
				//->setBlockTypes($this->values->blockTypes->toArray())
				;

			$this->_layout->addSlot($this->_slot);
			$config->setLayoutDefinition($this->_layout)->save();

			$this->arch->notify(
				'slot.save',
				$this->_('The layout has been successfully updated'),
				'success'
			);

			return $this->complete();
		}
    }
}