<?php

echo $this->import->component('UnitDetailHeaderBar', '~devtools/models/', $this['unit']);

if(!$this['unit']->storageExists()) {
    echo $this->html->flashMessage($this->_(
        'No storage exists for this unit'
    ), 'warning');
}

echo $this->html->attributeList($this['unit'])
    
    // Id
    ->addField('id', function($inspector) {
        return $inspector->getId();
    })

    // Canonical id
    ->addField('canonicalId', $this->_('Storage name'), function($inspector) {
        return $inspector->getCanonicalId();
    })

    // Type
    ->addField('type', function($inspector) {
        $output = ucfirst($inspector->getType());

        if($inspector->isVirtual()) {
            $output = [
                $output, ' ',
                $this->html->element('sup', '(virtual)')
            ];
        }

        return $output;
    })

    // Adapter
    ->addField('adapter', function($inspector) {
        return $inspector->getAdapterName();
    })

    // Connection
    ->addField('connection', function($inspector) {
        return $inspector->getAdapterConnectionName();
    })
    ;