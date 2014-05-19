<?php
$this->view
    ->linkCss($this->uri->themeAsset('css/screen.css'))
    ->linkJs(
        '//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js', null, null, 
        'window.jQuery || document.write(\'<script src="'.$this->uri->themeAsset('js/libs/jquery-1.8.3.min.js').'"><\/script>\')'
    )
    ->linkFootJs($this->uri->themeAsset('js/main.js'))
    ->linkFavicon($this->uri->themeAsset('favicon.ico', 'shared'))
    ;
?>
<div class="layout-pageArea">
    <header class="layout-header">
        <h1><?php echo $this->html->link('/', $this->application->getName()); ?></h1>
        
        
        <?php 
        if($this->_context->request->hasPath()) {
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

            $this->html->link('~front/', $this->_('Front end'))
                ->setIcon('home')
                ->isActive($this->context->request->isArea('front')),

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