<?php
$this->view
    ->linkCss($this->uri->themeAsset('css/screen.css'))
    ->linkCss($this->uri->themeAsset('js/main.js'))
    ->linkFootJs(
        '//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js', null, null, 
        'window.jQuery || document.write(\'<script src="'.$this->uri->themeAsset('js/libs/jquery-1.8.3.min.js').'"><\/script>\')'
    )
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

    <?php echo $this->html->notificationList(); ?>
    
    <div class="layout-contentArea">
        <?php echo $this->renderInnerContent(); ?>
    </div>
</div>