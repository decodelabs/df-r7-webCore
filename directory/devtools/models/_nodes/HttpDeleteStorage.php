<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\models\_nodes;

use DecodeLabs\Exceptional;
use DecodeLabs\Tagged as Html;

use df\arch;
use df\axis;

class HttpDeleteStorage extends arch\node\DeleteForm
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const ITEM_NAME = 'storage';

    protected $_unit;
    protected $_describer;

    protected function init(): void
    {
        $probe = new axis\introspector\Probe();
        $this->_unit = $probe->inspectUnit($this->request['unit']);

        if (!$this->_unit) {
            throw Exceptional::{'df/axis/unit/NotFound'}([
                'message' => 'Unit not found',
                'http' => 404
            ]);
        }

        if (!$this->_describer = $this->_unit->describeStorage($this->request['name'])) {
            throw Exceptional::{'df/axis/unit/NotFound'}([
                'message' => 'Storage not found',
                'http' => 404
            ]);
        }
    }

    protected function getInstanceId(): ?string
    {
        return $this->_unit->getId() . ':' . $this->_describer->name;
    }

    protected function createItemUi($container)
    {
        $container->addAttributeList($this->_describer)
            // Name
            ->addField('name', function ($storage) {
                return $storage->name;
            })

            // Type
            ->addField('type', function ($storage) {
                return $storage->type;
            })

            // Item count
            ->addField('itemCount', $this->_('Items'), function ($storage) {
                return $storage->itemCount;
            })

            // Size
            ->addField('size', function ($storage) {
                return Html::$number->fileSize($storage->size);
            })

            // Index size
            ->addField('indexSize', function ($storage) {
                return Html::$number->fileSize($storage->indexSize);
            })

            // Creation date
            ->addField('creationDate', $this->_('Created'), function ($storage) {
                return Html::$time->since($storage->creationDate);
            })
        ;
    }

    protected function apply()
    {
        $this->_unit->getAdapter()->destroyDescribedStorage($this->_describer);
    }
}
