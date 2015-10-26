<?php

echo $this->html->collectionList($files)
    ->addField('name', function($filePath, $context) {
        $name = substr($context->getKey(), 0, -4);
        return $this->html->link(
                '~ui/view?path='.$name,
                $name
            )
            ->setIcon('file')
            ->setDisposition('informative');
    })
    ->addField('created', function($filePath) {
        return $this->format->date(@filectime($filePath));
    })
    ->addField('modified', function($filePath) {
        return $this->format->timeFromNow(@filemtime($filePath));
    });