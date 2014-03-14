<?php

echo $this->import->component('UnitDetailHeaderBar', '~devtools/models/', $this['unit']);

echo $this->import->component('StorageList', '~devtools/models/')
    ->setCollection($this['backupList'])
    ->setUnitInspector($this['unit']);