<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\cache\_nodes;

use df\arch;
use df\fire;

class HttpNightfire extends arch\node\DeleteForm
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const ITEM_NAME = 'nightfire';
    public const IS_PERMANENT = false;

    protected $_cache;

    protected function init(): void
    {
        $this->_cache = fire\Cache::getInstance();
    }

    protected function createItemUi($container)
    {
        $container->addAttributeList($this->_cache)
            ->addField('name', function ($cache) {
                return 'Nightfire block cache';
            })
            ->addField('entries', function ($cache) {
                return $cache->count();
            });
    }

    protected function apply()
    {
        $this->_cache->clear();
    }
}
