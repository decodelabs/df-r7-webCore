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
        'version' => true,
        'actions' => true
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
            return $this->html->element('abbr', $id = $inspector->getCanonicalId())
                ->setAttribute('title', $inspector->getAdapterConnectionName().'/'.$id);
        });
    }

// Type
    public function addTypeField($list) {
        $list->addField('type', function($inspector) {
            $output = ucfirst($inspector->getType());

            if($adapter = $inspector->getAdapterName()) {
                $output = [
                    $output, ' ',
                    $this->html->element('sup', $adapter)
                ];
            }

            return $output;
        });
    }

// Version
    public function addVersionField($list) {
        $list->addField('version', function($inspector, $context) {
            if(!$inspector->isSchemaBasedStorageUnit()) {
                return;
            }

            $current = $inspector->getSchemaVersion();
            $max = $inspector->getDefinedSchemaVersion();

            if($current < $max) {
                $output = $this->html->icon('warning', $current.' / '.$max)->addClass('warning');
            } else {
                $output = $this->html->icon('tick', $current)->addClass('positive');
            }

            return $output;
        });
    }

// Actions
    public function addActionsField($list) {
        $list->addField('actions', function($inspector) {
            switch($inspector->getType()) {
                case 'cache':
                    return [
                        $this->html->link(
                                $this->uri->request('~devtools/models/clear-cache?unit='.$inspector->getGlobalId(), true),
                                $this->_('Clear cache')
                            )
                            ->setIcon('delete')
                    ];

                case 'table':
                    return [
                        $this->html->link(
                                $this->uri->request('~devtools/models/rebuild-table?unit='.$inspector->getGlobalId(), true),
                                $this->_('Rebuild table')
                            )
                            ->setIcon('refresh')
                            ->setDisposition('operative')
                    ];
            }
        });
    }
}