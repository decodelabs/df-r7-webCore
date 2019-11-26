<?php
use df\axis;
use df\core;
use df\opal;
use df\flex;

echo $this->apex->component('~devtools/models/UnitDetailHeaderBar', $unit);

if (!isset($rowList)) {
    echo $this->html->flashMessage($this->_(
        'No storage exists for this unit'
    ), 'warning');

    return;
}

$list = $this->html->collectionList($rowList)
    ->setErrorMessage($this->_('This storage unit is currently empty'));

foreach ($primitives as $primitive) {
    $list->addField($primitive->getName(), $primitive->getName(), function ($row) use ($primitive) {
        $name = $primitive->getName();

        if ($primitive instanceof opal\schema\Primitive_Date) {
            return Html::$time->date($row[$name]);
        }

        if ($primitive instanceof opal\schema\Primitive_DateTime
        || $primitive instanceof opal\schema\Primitive_Timestamp) {
            return Html::$time->date($row[$name]);
        }

        if ($primitive instanceof opal\schema\Primitive_Guid) {
            if ($row[$name] === null) {
                return null;
            }

            return Html::{'abbr'}('GUID')
                ->setTitle(flex\Guid::factory($row[$name]));
        }

        return $this->html->shorten($row[$name], 25);
    });
}

echo $list;
