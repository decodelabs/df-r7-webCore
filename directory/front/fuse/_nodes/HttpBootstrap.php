<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\fuse\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpBootstrap extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executeAsJs() {
        $path = df\Launchpad::$loader->findFile('apex/js/fuse/loader.js');

        return $this->http->fileResponse($path)
            ->setContentType('application/js');
    }
}