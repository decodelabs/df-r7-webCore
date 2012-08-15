<?php
echo $this->html->menuBar()
    ->addLinks(
        $this->html->backLink()
    );


$location = $this['counter']->getMergedLocation();

echo $this->html->attributeList($location)
    ->addField('Files', function($location) {
        return $this->format->number($location->countFiles());
    })
    ->addField('Lines', function($location) {
        return $this->format->number($location->countLines());
    })
    ->addField('Size', function($location) {
        return $this->format->fileSize($location->countBytes());
    })
    
        
    ->addField('phpFile', 'PHP Files', function($location) {
        return $this->format->number($location->getType('php')->countFiles());
    })
    ->addField('phpLines', 'PHP Lines', function($location) {
        return $this->format->number($location->getType('php')->countLines());
    })
    ->addField('phpSize', 'PHP Size', function($location) {
        return $this->format->fileSize($location->getType('php')->countBytes());
    })
    ;
    
?>

<hr />
<h3>Packages</h3>
<?php
echo $this->html->collectionList($this['packages'])
    ->addField('name', function($package) {
        return $package->name;
    })
    ->addField('priority', function($package) {
        return $package->priority;
    })
    ->addField('path', function($package) {
        return $this->html->string('<code>'.$this->esc($package->path).'</code>'); 
    })
    ->addField('size', function($package, $renderContext) {
        if(!$location = $this['counter']->getLocation($this->format->slug($package->name))) {
            return null;
        }

        $renderContext['location'] = $location;
        
        return $this->format->fileSize($location->countBytes());
    })
    ->addField('lines', function($package, $renderContext) {
        if(!$location = $renderContext['location']) {
            return null;
        }

        $phpCount = $location->getType('php')->countLines();
        $output = $this->html->string('<abbr title="PHP">'.$this->esc($this->format->number($phpCount)).'</abbr>');

        if($location->countTypes() > 1) {
            $totalCount = $location->countLines();

            if($totalCount > $phpCount) {
                $output->append(' / <abbr title="Total">'.$this->esc($this->format->number($totalCount)).'</abbr>');
            }
        }

        return $output;
    });
