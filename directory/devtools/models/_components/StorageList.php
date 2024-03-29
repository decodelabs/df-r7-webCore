<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_components;

use DecodeLabs\Tagged as Html;
use df\arch;

use df\axis;

class StorageList extends arch\component\CollectionList
{
    protected $fields = [
        'name' => true,
        'type' => true,
        'itemCount' => true,
        'size' => true,
        'indexSize' => true,
        'creationDate' => true,
        'actions' => true
    ];

    protected $inspector;

    public function setUnitInspector(axis\introspector\IUnitInspector $inspector)
    {
        $this->inspector = $inspector;
        return $this;
    }

    public function getUnitInspector()
    {
        return $this->inspector;
    }

    // Name
    public function addNameField($list)
    {
        $this->setErrorMessage($this->_('There are no backups of this unit\'s data'));

        $list->addField('name', function ($storage) {
            return $storage->name;
        });
    }

    // Type
    public function addTypeField($list)
    {
        $list->addField('type', function ($storage) {
            return $storage->type;
        });
    }

    // Item count
    public function addItemCountField($list)
    {
        $list->addField('itemCount', $this->_('Items'), function ($storage) {
            return $storage->itemCount;
        });
    }

    // Size
    public function addSizeField($list)
    {
        $list->addField('size', function ($storage) {
            return Html::$number->fileSize($storage->size);
        });
    }

    // Index size
    public function addIndexSizeField($list)
    {
        $list->addField('indexSize', function ($storage) {
            return Html::$number->fileSize($storage->indexSize);
        });
    }

    // Creation date
    public function addCreationDateField($list)
    {
        $list->addField('creationDate', $this->_('Created'), function ($storage) {
            return Html::$time->since($storage->creationDate);
        });
    }

    // Actions
    public function addActionsField($list)
    {
        if (!$this->inspector) {
            return;
        }

        $list->addField('actions', function ($storage) {
            return $this->html->link(
                $this->uri('~devtools/models/delete-storage?unit=' . $this->inspector->getId() . '&name=' . $storage->name, true),
                $this->_('Delete storage')
            )
                ->setIcon('delete');
        });
    }
}
