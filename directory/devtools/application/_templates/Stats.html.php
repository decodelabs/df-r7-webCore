<?php
echo $this->html->menuBar()
    ->addLinks(
        $this->html->backLink()
    );


$location = $probes->getAll();
$location->sortByLines();
$list = $location->getTypes();
$list[] = $location->getTotals();

echo $this->html->collectionList($list)
    ->addField('extension', function($location, $context) {
        if($location->extension == 'TOTAL') {
            $context->rowTag->addClass('active');
        }

        return $location->extension;
    })
    ->addField('files', function($location) {
        return $this->format->number($location->files);
    })
    ->addField('lines', function($location) {
        return $this->format->number($location->lines);
    })
    ->addField('size', function($location) {
        return $this->format->fileSize($location->bytes);
    });
?>

<hr />
<h3>Packages</h3>
<?php

echo $this->html->collectionList($packages)
    ->addField('name', function($package) {
        return $package->name;
    })
    ->addField('priority', function($package) {
        return $package->priority;
    })
    ->addField('path', function($package) {
        return $this->html('<code>'.$this->esc($package->path).'</code>');
    })
    ->addField('size', function($package, $renderContext) use($probes) {
        if(!$location = $probes[$package->name]) {
            return null;
        }

        return $this->format->fileSize($location->getTotals()->bytes);
    })
    ->addField('lines', function($package, $renderContext) use($probes) {
        if(!$location = $probes[$package->name]) {
            return null;
        }

        $phpCount = $location['php']->lines;
        $output = $this->html('<abbr title="PHP">'.$this->esc($this->format->number($phpCount)).'</abbr>');

        if($location->countTypes() > 1) {
            $totalCount = $location->getTotals()->lines;

            if($totalCount > $phpCount) {
                $output->append(' / <abbr title="Total">'.$this->esc($this->format->number($totalCount)).'</abbr>');
            }
        }

        return $output;
    });
