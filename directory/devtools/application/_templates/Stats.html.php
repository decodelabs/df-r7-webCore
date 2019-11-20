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
    ->addField('extension', function ($location, $context) {
        if ($location->extension == 'TOTAL') {
            $context->rowTag->addClass('active');
        }

        return $location->extension;
    })
    ->addField('files', function ($location) {
        return $this->format->number($location->files);
    })
    ->addField('lines', function ($location) {
        return $this->format->number($location->lines);
    })
    ->addField('size', function ($location) {
        return $this->format->fileSize($location->bytes);
    });
?>

<hr />
<h3>Packages</h3>
<?php

echo $this->html->collectionList($packages)
    ->addField('name', function ($package) {
        return $package->name;
    })
    ->addField('priority', function ($package) {
        return $package->priority;
    })
    ->addField('path', function ($package) {
        return Html::{'code'}($package->path);
    })
    ->addField('size', function ($package, $renderContext) use ($probes) {
        if (!$location = $probes[$package->name]) {
            return null;
        }

        return $this->format->fileSize($location->getTotals()->bytes);
    })
    ->addField('lines', function ($package, $renderContext) use ($probes) {
        if (!$location = $probes[$package->name]) {
            return;
        }

        $phpCount = $location['php']->lines;

        yield Html::{'abbr'}($this->format->number($phpCount), [
            'title' => 'PHP'
        ]);

        if ($location->countTypes() > 1) {
            $totalCount = $location->getTotals()->lines;

            if ($totalCount > $phpCount) {
                yield ' / ';
                yield Html::{'abbr'}($this->format->number($totalCount), [
                    'title' => 'Total'
                ]);
            }
        }
    });
