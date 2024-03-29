<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_components;

use DecodeLabs\Tagged as Html;

use df\arch;

class UnitList extends arch\component\CollectionList
{
    protected $fields = [
        'id' => true,
        'canonicalId' => true,
        'type' => true,
        'version' => true,
        'actions' => true
    ];


    // Id
    public function addIdField($list)
    {
        $list->addField('id', function ($inspector) {
            return $this->apex->component('~devtools/models/UnitLink', $inspector)
                ->setDisposition('informative');
        });
    }

    // Canonical id
    public function addCanonicalIdField($list)
    {
        $list->addField('canonicalId', $this->_('Storage id'), function ($inspector) {
            return Html::{'abbr'}($id = $inspector->getCanonicalId())
                ->setTitle($inspector->getAdapterConnectionName() . '/' . $id);
        });
    }

    // Type
    public function addTypeField($list)
    {
        $list->addField('type', function ($inspector) {
            $output = ucfirst($inspector->getType());

            if ($adapter = $inspector->getAdapterName()) {
                $output = [
                    $output, ' ',
                    Html::{'sup'}($adapter)
                ];
            }

            return $output;
        });
    }

    // Version
    public function addVersionField($list)
    {
        $list->addField('version', function ($inspector, $context) {
            if (!$inspector->isSchemaBasedStorageUnit()) {
                return;
            }

            $current = $inspector->getSchemaVersion();
            $max = $inspector->getDefinedSchemaVersion();

            if ($current < $max) {
                $output = $this->html->icon('warning', $current . ' / ' . $max)->addClass('warning');
            } else {
                $output = $this->html->icon('tick', $current)->addClass('positive');
            }

            return $output;
        });
    }

    // Actions
    public function addActionsField($list)
    {
        $list->addField('actions', function ($inspector) {
            switch ($inspector->getType()) {
                case 'cache':
                    return [
                        $this->html->link(
                            $this->uri('~devtools/models/clear-cache?unit=' . $inspector->getId(), true),
                            $this->_('Clear cache')
                        )
                            ->setIcon('delete')
                    ];

                case 'table':
                    return [
                        $this->html->link(
                            $this->uri('~devtools/models/rebuild-table?unit=' . $inspector->getId(), true),
                            $this->_('Rebuild table')
                        )
                            ->setIcon('refresh')
                            ->setDisposition('operative')
                    ];
            }
        });
    }
}
