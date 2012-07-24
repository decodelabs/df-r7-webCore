<?php
echo $this->html->collectionList($this['entryList']->getEntries())
	->setErrorMessage($this->_('This menu has no entries'))

	// Id
	->addField('id', function($entry, $view) {
		return $entry->getId();
	})

	// Type
	->addField('type', function($entry, $view) {
		return $entry->getType();
	})

	// Weight
	->addField('weight', function($entry, $view) {
		return $entry->getWeight();
	})

	// Preview
	->addField('preview', function($entry, $view) {
		switch($entry->getType()) {
			case 'Link':
				return $view->html->link($entry);

			case 'Spacer':
				return $view->html->element('hr');
		}
	})

	// Description
	->addField('description')
	;