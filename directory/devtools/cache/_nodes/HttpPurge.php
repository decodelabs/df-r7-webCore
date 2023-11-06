<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\cache\_nodes;

use DecodeLabs\Stash;
use df\arch;
use df\core;

class HttpPurge extends arch\node\DeleteForm
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const ITEM_NAME = 'cache';
    public const IS_PERMANENT = false;

    protected function apply()
    {
        core\cache\Base::purgeAll();
        Stash::purgeAll();
    }
}
