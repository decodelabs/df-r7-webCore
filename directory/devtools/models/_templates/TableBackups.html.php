<?php

echo $this->apex->component('~devtools/models/UnitDetailHeaderBar', $this['unit']);

echo $this->apex->component('~devtools/models/StorageList')
    ->setCollection($this['backupList'])
    ->setUnitInspector($this['unit']);