<?php

use DecodeLabs\Disciple;

echo $this->html->menu()
    ->addLinks(
        $this->html->link('account/login', $this->_('Log in'))
            ->addAccessLock(Disciple::isLoggedIn())
            ->shouldHideIfInaccessible(true)
            ->setIcon('lock'),
        $this->html->link('account/', $this->_('My account'))
            ->shouldHideIfInaccessible(true)
            ->setIcon('profile'),
        $this->html->link('~admin/', $this->_('Admin control panel'))
            ->setIcon('controlPanel'),
        $this->html->link('~devtools/', $this->_('Developer tools'))
            ->setIcon('tool')
    );
