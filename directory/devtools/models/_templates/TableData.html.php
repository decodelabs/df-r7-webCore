<?php
use df\axis;
use df\core;
use df\opal;

echo $this->apex->component('~devtools/models/UnitDetailHeaderBar', $this['unit']);

if(null === ($rowList = $this['rowList'])) {
    echo $this->html->flashMessage($this->_(
        'No storage exists for this unit'
    ), 'warning');

    return;
}

$list = $this->html->collectionList($rowList)
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
        }

        if($primitive instanceof opal\schema\Primitive_Guid) {
            return $this->html('abbr', 'GUID')
                ->setTitle(core\string\Uuid::factory($row[$name]));
        }

        return $this->html->shorten($row[$name], 25);
    });
}

echo $list;