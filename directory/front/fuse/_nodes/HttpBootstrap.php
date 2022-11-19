<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\fuse\_nodes;

use DecodeLabs\R7\Legacy;

use df\arch;

class HttpBootstrap extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executeAsJs()
    {
        $path = Legacy::getLoader()->findFile('apex/js/fuse/loader.js');

        return Legacy::$http->fileResponse($path)
            ->setContentType('application/js');
    }
}
