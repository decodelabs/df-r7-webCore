<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\cache\_nodes;

use DecodeLabs\Stash;
use df\arch;

class HttpPurge extends arch\node\DeleteForm
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const ITEM_NAME = 'cache';
    public const IS_PERMANENT = false;

    protected function apply()
    {
        Stash::purge();
    }
}
