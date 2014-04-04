<?php
use df\axis;
use df\opal;

echo $this->import->component('UnitDetailHeaderBar', '~devtools/models/', $this['unit']);

$list = $this->html->collectionList($this['rowList'])
    ->setErrorMessage($this->_('This storage unit is currently empty'));

foreach($this['primitives'] as $primitive) {
    $list->addField($primitive->getName(), $primitive->getName(), function($row) use($primitive) {
        $name = $primitive->getName();

        if($primitive instanceof opal\schema\Primitive_Date) {
            return $this->html->date($row[$name]);
        }

        if($primitive instanceof opal\schema\Primitive_DateTime
        || $primitive instanceof opal\schema\Primitive_Timestamp) {
            return $this->html->dateTime($row[$name]);
        } else {
            return $this->html->shorten($row[$name], 25);
        }
    });
}

echo $list;