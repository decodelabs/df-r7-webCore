<?php
echo $this->html->menuBar()
    ->addLinks(
        $this->html->backLink()
    );


$location = $this['counter']->getMergedLocation();

echo $this->html->attributeList($location)
    ->addField('Files', function($location, $view) {
        return $view->format->number($location->countFiles());
    })
    ->addField('Lines', function($location, $view) {
        return $view->format->number($location->countLines());
    })
    ->addField('Size', function($location, $view) {
        return $view->format->fileSize($location->countBytes());
    })
    
    ->addField('---', function($location, $view) {
        return '';
    })
    
    ->addField('phpFile', 'PHP Files', function($location, $view) {
        return $view->format->number($location->getType('php')->countFiles());
    })
    ->addField('phpLines', 'PHP Lines', function($location, $view) {
        return $view->format->number($location->getType('php')->countLines());
    })
    ->addField('phpSize', 'PHP Size', function($location, $view) {
        return $view->format->fileSize($location->getType('php')->countBytes());
    })
    ;
    
?>

<hr />
<h3>Packages</h3>
<?php
echo $this->html->collectionList($this['packages'])
    ->addField('name', function($package, $view) {
        return $package->name;
    })
    ->addField('priority', function($package, $view) {
        return $package->priority;
    })
    ->addField('path', function($package, $view) {
        return $view->html->string('<code>'.$view->esc($package->path).'</code>'); 
    });
