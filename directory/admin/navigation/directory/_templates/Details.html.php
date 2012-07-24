<?php
echo $this->html->menuBar()
	->addLinks(
		$this->html->link(
				$this->uri->request('~admin/navigation/directory/edit?menu='.$this['menu']->getId()->path->toString(), true),
				$this->_('Edit menu')
			)
			->setIcon('edit'),

        '|',

		$this->html->backLink()
	);


echo $this->html->element('h3', $this->_('Basic details'));

echo $this->html->attributeList($this['menu'])

	// Id
	->addField('id', $this->_('Full ID'), function($menu, $view) {
		return $menu->getId();
	})

	// Name
	->addField('name', function($menu, $view) {
		return $menu->getDisplayName();
	})

	// Area
	->addField('area', function($menu, $view) {
		return $menu->getId()->path->getFirst();
	})

	// Parent
	->addField('parent', function($menu, $view) {
		if($subId = $menu->getSubId()) {
			$id = substr($menu->getId()->path->toString(), 0, -strlen($subId) - 1);

			return $view->html->link(
					$view->uri->request('~admin/navigation/directory/details?menu='.$id, true),
					'Directory://'.$id
				)
				->setIcon('menu')
				->setDisposition('informative');
		}
	})

	// Package
	->addField('package', $this->_('Delegate package'), function($menu, $view) {
		return $menu->getSubId();
	})

	// Delegates
	->addField('delegates', function($menu, $view) {
		$delegates = $menu->getDelegates();

		if(!empty($delegates)) {
			return $view->html->bulletList($delegates)
				->setRenderer(function($delegate, $view) {
					if($delegate->getSourceId() == 'Directory') {
						return $view->html->link(
								$view->uri->request('~admin/navigation/directory/details?menu='.$delegate->getId()->path->toString()),
								$delegate->getId()
							)
							->setIcon('menu')
							->setDisposition('informative');
					} else {
						return $view->html->icon('menu', $delegate->getId())
							->setTitle($delegate->getDisplayName());
					}
				});
		}
	})
	;


echo $this->html->element('h3', $this->_('Entries'));

echo $this->import->template(
		'elements/MenuEntryList.html',
		'~admin/navigation/'
	)
	->setArgs(['entryList' => $this['entryList']]);