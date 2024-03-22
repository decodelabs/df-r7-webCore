<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\cache\_nodes;

use df\arch;
use df\user;

class HttpSession extends arch\node\DeleteForm
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const ITEM_NAME = 'cache';
    public const IS_PERMANENT = false;

    protected $_cache;

    protected function init(): void
    {
        $this->_cache = user\session\Cache::getInstance();
    }

    protected function createItemUi($container)
    {
        $container->addAttributeList($this->_cache)
            ->addField('name', function ($cache) {
                return 'Session storage cache';
            });
    }

    protected function apply()
    {
        $this->_cache->clear();
    }
}
