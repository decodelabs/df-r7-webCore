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
use df\fire;
    
class HttpNightfire extends arch\form\template\Delete {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'nightfire';
    const IS_PERMANENT = false;

    protected $_cache;

    protected function init() {
        $this->_cache = fire\Cache::getInstance();
    }

    protected function createItemUi($container) {
        $container->addAttributeList($this->_cache)
            ->addField('name', function($cache) {
                return 'Nightfire block cache';
            })
            ->addField('entries', function($cache) {
                return $cache->count();
            });
    }

    protected function apply() {
        $this->_cache->clear();
    }
}