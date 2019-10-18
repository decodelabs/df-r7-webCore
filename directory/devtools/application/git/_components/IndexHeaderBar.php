<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\application\git\_components;

use df;
use df\core;
use df\apex;
use df\arch;

class IndexHeaderBar extends arch\component\HeaderBar
{
    protected $_icon = 'package';

    protected function _getDefaultTitle()
    {
        return $this->_('Git packages');
    }
}
