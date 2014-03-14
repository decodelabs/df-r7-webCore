<?php
$list = $this->html->attributeList($this['result'])

    ->addField('time', function($result) {
        return $result->getTimer();
    })
    ->addField('message', function($result) {
        return $this->html->element('code', $result->getOutput());
    });

if($this['result']->hasError()) {
    $list->addField('error', function($result) {
        return $this->html->element('code', $result->getError());
    });
}

echo $list;