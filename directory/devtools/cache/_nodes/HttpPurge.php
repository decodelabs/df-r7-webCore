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

class HttpPurge extends arch\node\DeleteForm {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'cache';
    const IS_PERMANENT = false;

    protected function apply() {
        core\cache\Base::purgeAll();
    }
}