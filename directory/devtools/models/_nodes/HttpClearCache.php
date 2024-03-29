<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\models\_nodes;

use DecodeLabs\Exceptional;
use df\arch;

use df\axis;

class HttpClearCache extends arch\node\ConfirmForm
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const DISPOSITION = 'negative';

    protected $_inspector;

    protected function init(): void
    {
        $probe = new axis\introspector\Probe();

        if (!$this->_inspector = $probe->inspectUnit($this->request['unit'])) {
            throw Exceptional::{'df/axis/unit/NotFound'}([
                'message' => 'Unit not found',
                'http' => 404
            ]);
        }

        if ($this->_inspector->getType() != 'cache') {
            throw Exceptional::{'df/axis/unit/Domain,Forbidden'}([
                'message' => 'Unit not a cache',
                'http' => 403
            ]);
        }
    }

    protected function getInstanceId(): ?string
    {
        return $this->_inspector->getId();
    }

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to clear this cache?');
    }

    protected function createItemUi($container)
    {
        $container->addAttributeList($this->_inspector)
            ->addField('unit', function ($inspector) {
                return $inspector->getId();
            })
            ->addField('backend', function ($inspector) {
                return $inspector->getAdapterName();
            })
            ->addField('entries', function ($inspector) {
                return $inspector->getUnit()->count();
            });
    }

    protected function customizeMainButton($button)
    {
        $button->setBody($this->_('Clear'))
            ->setIcon('delete');
    }

    protected function apply()
    {
        $this->_inspector->getUnit()->clear();
    }
}
