<?php

echo $this->apex->component('~devtools/theme/layouts/DetailHeaderBar', $this['layout']);

echo $this->html->attributeList($this['layout'])

    // Id
    ->addField('id', function($layout) {
        return $layout->getId();
    })

    // Name
    ->addField('name', function($layout) {
        return $layout->getName();
    })

    // Areas
    ->addField('areas', function($layout) {
        return implode(', ', $layout->getAreas());
    })

    // Static
    ->addField('isStatic', function($layout) {
        return $this->html->booleanIcon($layout->isStatic());
    });