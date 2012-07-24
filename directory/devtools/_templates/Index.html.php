<?php
$menu = $this->html->menu()
	->addLink(
		$this->html->link('~devtools/stats', $this->_('View application file stats'))
			->setIcon('stats')
	);

if($this->context->arch->actionExists('~devtools/regenerate-test-db')) {
	$menu->addLink(
		$this->html->link('~devtools/regenerate-test-db', $this->_('Regenerate test DB'))
			->setIcon('info')
	);
} else {
	$menu->addLink(
		$this->html->link('~devtools/setup-user', $this->_('Setup root user'))
			->setIcon('profile')
	);
}

echo $menu;