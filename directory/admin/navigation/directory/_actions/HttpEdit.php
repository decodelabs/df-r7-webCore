<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\navigation\directory\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;
    
class HttpEdit extends arch\form\Action {

    protected $_menu;

    protected function _init() {
        if(!$this->_menu = arch\navigation\menu\Base::factory($this->_context, 'directory://'.$this->request->query['menu'])) {
            $this->throwError(404, 'Menu not found');
        }
    }

    protected function _getDataId() {
        return $this->_menu->getId()->path->toString();
    }

    protected function _setupDelegates() {
        core\stub('Add menu delegate selector and entry builder');
    }

    protected function _createUi() {

    }
}