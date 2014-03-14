<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class UnitList extends arch\component\template\CollectionList {

    protected $_fields = [
        'id' => true,
        'canonicalId' => true,
        'type' => true,
        'adapter' => true,
        'connection' => true
    ];


// Id
    public function addIdField($list) {
        $list->addField('id', function($inspector) {
            return $this->import->component('UnitLink', '~devtools/models/', $inspector);
        });
    }

// Canonical id
    public function addCanonicalIdField($list) {
        $list->addField('canonicalId', $this->_('Storage id'), function($inspector) {
            return $inspector->getCanonicalId();
        });
    }

// Type
    public function addTypeField($list) {
        $list->addField('type', function($inspector) {
            return ucfirst($inspector->getType());
        });
    }

// Adapter
    public function addAdapterField($list) {
        $list->addField('adapter', function($inspector) {
            return $inspector->getAdapterName();
        });
    }

// Connection
    public function addConnectionField($list) {
        $list->addField('connection', function($inspector) {
            return $this->html->shorten($inspector->getAdapterConnectionName(), 35);
        });
    }
}