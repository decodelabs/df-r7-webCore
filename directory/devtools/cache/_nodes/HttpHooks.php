<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\cache\_nodes;

use DecodeLabs\Stash;
use DecodeLabs\Stash\Store;
use df\arch;

class HttpHooks extends arch\node\DeleteForm
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const ITEM_NAME = 'cache';
    public const IS_PERMANENT = false;

    protected Store $_cache;

    protected function init(): void
    {
        $this->_cache = Stash::load('mesh.hook');
    }

    protected function createItemUi($container)
    {
        $container->addAttributeList($this->_cache)
            ->addField('name', function ($cache) {
                return 'Mesh event hook cache';
            });
    }

    protected function apply()
    {
        $this->_cache->clear();
    }
}
