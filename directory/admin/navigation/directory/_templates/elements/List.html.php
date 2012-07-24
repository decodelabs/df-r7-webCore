<?php
echo $this->html->collectionList($this['menuList'])
	->setErrorMessage($this->_('There are currently no system menus to display'))

	// Name
	->addField('name', function($menu, $view) {
		$idString = $menu->getId()->path->toString();
		$parts = explode('/', $idString);
		array_pop($parts);
		$path = implode('/', $parts);

		return $view->html->link(
				$view->uri->request('~admin/navigation/directory/details?menu='.$idString, true),
				$view->html->string(
					'<span class="state-lowPriority">'.$view->esc($path.'/').'</span>'.
					$view->esc($menu->getDisplayName())
				)
			)
			->setIcon('menu')
			->setDisposition('informative');
	})


	// Packages
	->addField('packages', function($menu, $view) {
		$output = array();

		foreach($menu->getDelegates() as $delegate) {
			$idString = $delegate->getId()->path->toString();

			if(!$subId = $delegate->getSubId()) {
				continue;
			}

			$output[] = $view->html->link(
					$view->uri->request('~admin/navigation/directory/details?menu='.$idString, true),
					$view->format->name($subId)
				)
				->setIcon('plugin');
		}

		return $view->html->string(implode(', ', $output));
	})

	// Actions
	->addField('actions', function($menu, $view) {
		return [
			$view->html->link(
					$view->uri->request('~admin/navigation/directory/edit?menu='.$menu->getId()->path->toString(), true),
					$view->_('Edit')
				)
				->setIcon('edit')
		];
	})
	;