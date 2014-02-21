<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class IndexHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'user';

    protected function _getDefaultTitle() {
        return $this->_('User management');
    }
}