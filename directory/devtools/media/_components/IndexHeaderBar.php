<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\media\_components;

use df;
use df\core;
use df\apex;
use df\arch;

class IndexHeaderBar extends arch\component\HeaderBar
{
    protected $icon = 'folder';

    protected function getDefaultTitle()
    {
        return $this->_('Media tools');
    }
}
