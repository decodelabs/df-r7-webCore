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
use df\user;
    
class HttpSession extends arch\form\template\Delete {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'cache';
    const IS_PERMANENT = false;

    protected $_cache;
    protected $_shellCache;

    protected function _init() {
        $this->_cache = user\session\Cache::getInstance();
        $this->_shellCache = user\session\perpetuator\Shell_Cache::getInstance();
    }

    protected function _renderItemDetails($container) {
        $container->addAttributeList($this->_cache)
            ->addField('name', function($cache) {
                return 'Session storage cache';
            })
            ->addField('entries', function($cache) {
                return $cache->count();
            })
            ->addField('shellSessions', function() {
                return $this->_shellCache->count();
            });

        $container->addCheckbox(
                'deleteShellPerpetuator',
                $this->values->deleteShellPerpetuator,
                $this->_('Also delete the shell session perpetuator cache')
            );
    }

    protected function _deleteItem() {
        $this->_cache->clear();

        if($this->values['deleteShellPerpetuator']) {
            $this->_shellCache->clear();
        }
    }
}