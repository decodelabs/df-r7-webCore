<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\cache\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\mesh;

class HttpHooks extends arch\node\DeleteForm {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'cache';
    const IS_PERMANENT = false;

    protected $_cache;

    protected function init() {
        $this->_cache = mesh\event\HookCache::getInstance();
    }

    protected function createItemUi($container) {
        $container->addAttributeList($this->_cache)
            ->addField('name', function($cache) {
                return 'Mesh event hook cache';
            })
            ->addField('entries', function($cache) {
                return $cache->count();
            });
    }

    protected function apply() {
        $this->_cache->clear();
    }
}