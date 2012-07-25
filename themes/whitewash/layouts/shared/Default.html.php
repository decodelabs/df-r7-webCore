<?php
$this->view
    ->linkCss($this->uri->themeAsset('screen.css'))
    ->linkFavicon($this->uri->themeAsset('favicon.ico', 'shared'))
    ;
?>
<div class="layout-pageArea">
    <header class="layout-header">
        <h1><?php echo $this->html->link('/', $this->application->getName()); ?></h1>
        
        
        <?php 
        if($this->_context->getRequest()->hasPath()) {
            echo $this->html->breadcrumbList(true);
        } 
        ?>
    </header>

    <?php echo $this->html->notificationList(); ?>
    
    <div class="layout-contentArea">
        <?php echo $this->renderInnerContent(); ?>
    </div>
</div>