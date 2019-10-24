<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\axis;

use DecodeLabs\Glitch;

class HttpCacheStats extends arch\node\Base
{
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml()
    {
        $view = $this->apex->view('CacheStats.html');

        $view['unit'] = (new axis\introspector\Probe())
            ->inspectUnit($this->request['unit']);

        if ($view['unit']->getType() != 'cache') {
            throw Glitch::EForbidden([
                'message' => 'Unit is not a cache',
                'http' => 403
            ]);
        }

        $view['stats'] = $view['unit']->getUnit()->getCacheStats();
        return $view;
    }
}
