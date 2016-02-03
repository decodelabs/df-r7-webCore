<div class="layout-pageArea">
    <header class="layout-header">
        <h1><?php echo $this->html->link('/', $this->application->getName()); ?></h1>

        <?php
        echo $this->html('nav.user', function() {
            if($this->user->isLoggedIn()) {
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

        if($this->context->request->hasPath()) {
            echo $this->html->breadcrumbList(true);
        }
        ?>
    </header>

    <?php echo $this->html->flashList(); ?>

    <div class="layout-contentArea">
        <?php echo $this->renderInnerContent(); ?>
    </div>

    <footer>
    <?php
    echo $this->html->menuBar()
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
        ->chainIf(!$this->context->application->isProduction(), function($menu) {
            $menu->addLinks(
                $this->html->link('~ui/', $this->_('UI testing'))
                    ->setIcon('theme')
                    ->isActive($this->context->request->isArea('ui'))
            );
        });
    ?>
    </footer>
</div>