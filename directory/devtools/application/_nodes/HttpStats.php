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

use DecodeLabs\R7\Legacy;

class HttpStats extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml()
    {
        clearstatcache();

        $scanner = new flex\code\Scanner(null, [
            new flex\code\probe\Counter()
        ]);

        $scanner->addFrameworkPackageLocations();
        $probes = $scanner->scan()['counter'];

        $view = $this->apex->view('Stats.html')
            ->setSlot('probes', $probes)
            ->setSlot('packages', Legacy::getLoader()->getPackages());

        return $view;
    }
}
