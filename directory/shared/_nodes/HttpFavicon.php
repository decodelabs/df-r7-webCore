<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\shared\_nodes;

use DecodeLabs\R7\Legacy;

use df\arch;

class HttpFavicon extends arch\node\Base
{
    public const OPTIMIZE = true;
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executeAsIco()
    {
        return Legacy::$http->fileResponse(
            $this->findFile('apex/themes/shared/assets/favicon.ico')
        );
    }
}
