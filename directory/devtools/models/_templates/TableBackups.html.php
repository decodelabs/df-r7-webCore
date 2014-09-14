<?php

echo $this->import->component('~devtools/models/UnitDetailHeaderBar', $this['unit']);

echo $this->import->component('~devtools/models/StorageList')
    ->setCollection($this['backupList'])
    ->setUnitInspector($this['unit']);