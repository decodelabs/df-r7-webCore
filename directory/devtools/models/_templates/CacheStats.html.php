<?php

echo $this->import->component('UnitDetailHeaderBar', '~devtools/models/', $this['unit']);

$list = $this->html->attributeList($this['stats']);

foreach($this['stats'] as $key => $value) {
    if($key == 'size') {
        $list->addField($key, function($stats) {
            return $this->format->fileSize($stats['size']);
        });
    } else {
        $list->addField($key);
    }
}

echo $list;