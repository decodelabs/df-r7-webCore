<?php
use DecodeLabs\Tagged\Html;

echo $this->apex->component('~devtools/models/UnitDetailHeaderBar', $unit);

if (!$unit->storageExists()) {
    echo $this->html->flashMessage($this->_(
        'No storage exists for this unit'
    ), 'warning');
}

echo $this->html->attributeList($unit)

    // Id
    ->addField('id', function ($inspector) {
        return $inspector->getId();
    })

    // Canonical id
    ->addField('canonicalId', $this->_('Storage name'), function ($inspector) {
        return $inspector->getCanonicalId();
    })

    // Type
    ->addField('type', function ($inspector) {
        $output = ucfirst($inspector->getType());

        if ($inspector->isVirtual()) {
            $output = [
                $output, ' ',
                Html::{'sup'}('(virtual)')
            ];
        }

        return $output;
    })

    // Version
    ->addField('version', function ($inspector, $context) {
        if (!$inspector->isSchemaBasedStorageUnit()) {
            return;
        }

        $current = $inspector->getSchemaVersion();
        $max = $inspector->getDefinedSchemaVersion();

        if ($current < $max) {
            $output = $this->html->icon('warning', $current.' / '.$max)->addClass('warning');
        } else {
            $output = $this->html->icon('tick', $current)->addClass('positive');
        }

        return $output;
    })

    // Adapter
    ->addField('adapter', function ($inspector) {
        return $inspector->getAdapterName();
    })

    // Connection
    ->addField('connection', function ($inspector) {
        return $inspector->getAdapterConnectionName();
    })
    ;
