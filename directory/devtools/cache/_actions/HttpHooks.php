<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\cache\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\mesh;
    
class HttpHooks extends arch\form\template\Delete {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'cache';
    const IS_PERMANENT = false;

    protected $_cache;

    protected function _init() {
        $this->_cache = mesh\event\HookCache::getInstance($this->application);
    }

    protected function _renderItemDetails($container) {
        $container->addAttributeList($this->_cache)
            ->addField('name', function($cache) {
                return 'Mesh event hook cache';
            })
            ->addField('entries', function($cache) {
                return $cache->count();
            });
    }

    protected function _deleteItem() {
        $this->_cache->clear();
    }
}