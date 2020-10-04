<?php
use DecodeLabs\Tagged\Html;

echo Html::{'div.layout-pageArea.floated'}(function () {
    yield Html::{'header.layout-header'}(function () {
        yield Html::h1($this->html->link('/', $this->app->getName()));

        yield Html::{'nav.user'}(function () {
            if ($this->user->isLoggedIn()) {
                yield $this->_('Logged in as %n%. ', [
                    '%n%' => $this->user->client->getFullName()
                ]);

                yield $this->html->link('account/logout', 'Log out')
                    ->setIcon('user')
                    ->setDisposition('transitive');
            } else {
                yield $this->_('Browsing as a guest ');
                yield $this->html->link(
                        $this->uri('account/login', true),
                        $this->_('Log in now')
                    )
                    ->setIcon('user')
                    ->setDisposition('transitive');
            }
        });

        if ($this->context->request->hasPath()) {
            yield $this->html->breadcrumbList()->addSitemapEntries();
        }
    });

    yield Html::raw($this->html->flashList());
    yield Html::{'div.layout-contentArea'}(Html::raw($this->renderInnerContent()));

    yield Html::footer(function () {
        yield $this->html->menuBar()
            ->addLinks(
                $this->html->link('~admin/', $this->_('Admin control panel'))
                    ->setIcon('admin')
                    ->isActive($this->context->request->isArea('admin')),

                $this->html->link('~mail/', $this->_('Mail centre'))
                    ->setIcon('mail')
                    ->isActive($this->context->request->isArea('mail'))
                    ->shouldHideIfInaccessible(true),

                $this->html->link('~devtools/', $this->_('Devtools'))
                    ->setIcon('debug')
                    ->isActive($this->context->request->isArea('devtools'))
                    ->shouldHideIfInaccessible(true)
            )
            ->chainIf(!$this->context->app->isProduction(), function ($menu) {
                $menu->addLinks(
                    $this->html->link('~ui/', $this->_('UI testing'))
                        ->setIcon('theme')
                        ->isActive($this->context->request->isArea('ui'))
                );
            });
    });
});
