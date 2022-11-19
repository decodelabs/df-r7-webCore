<?php

echo $this->apex->component('~devtools/models/UnitDetailHeaderBar', $unit);

echo $this->apex->component('~devtools/models/StorageList')
    ->setCollection($backupList)
    ->setUnitInspector($unit);
