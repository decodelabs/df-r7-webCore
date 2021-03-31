<?php
use DecodeLabs\Tagged as Html;

echo $this->apex->component('~devtools/models/UnitDetailHeaderBar', $unit);

$list = $this->html->attributeList($stats);

foreach ($stats as $key => $value) {
    if ($key == 'size') {
        $list->addField($key, function ($stats) {
            return Html::$number->fileSize($stats['size']);
        });
    } else {
        $list->addField($key);
    }
}

echo $list;
