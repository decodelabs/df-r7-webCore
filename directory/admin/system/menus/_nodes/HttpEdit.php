<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\menus\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;

use DecodeLabs\Glitch;

class HttpEdit extends arch\node\Form
{
    protected $_menu;

    protected function init()
    {
        $this->_menu = arch\navigation\menu\Base::factory($this->context, 'directory://'.$this->request['menu']);
    }

    protected function getInstanceId()
    {
        return $this->_menu->getId()->path->toString();
    }

    protected function loadDelegates()
    {
        Glitch::incomplete('Add menu delegate selector and entry builder');
    }

    protected function createUi()
    {
    }
}
