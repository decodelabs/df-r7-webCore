<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\application\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\flex;

class HttpFiles extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml()
    {
        dd(get_included_files());
    }
}
