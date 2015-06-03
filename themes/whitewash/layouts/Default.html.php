<div class="layout-pageArea">
    <header class="layout-header">
        <h1><?php echo $this->html->link('/', $this->application->getName()); ?></h1>
        
        
        <?php 
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

            /*
            $this->html->link('~front/', $this->_('Front end'))
                ->setIcon('home')
                ->isActive($this->context->request->isArea('front')),
            */

            $this->html->link('~mail/', $this->_('Mail centre'))
                    ->setIcon('mail')
                    ->isActive($this->context->request->isArea('mail')),

            $this->html->link('~devtools/', $this->_('Devtools'))
                ->setIcon('debug')
                ->isActive($this->context->request->isArea('devtools'))
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