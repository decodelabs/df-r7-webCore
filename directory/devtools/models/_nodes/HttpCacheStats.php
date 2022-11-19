<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_nodes;

use DecodeLabs\Exceptional;
use df\arch;

use df\axis;

class HttpCacheStats extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml()
    {
        $view = $this->apex->view('CacheStats.html');

        $view['unit'] = (new axis\introspector\Probe())
            ->inspectUnit($this->request['unit']);

        if ($view['unit']->getType() != 'cache') {
            throw Exceptional::Forbidden([
                'message' => 'Unit is not a cache',
                'http' => 403
            ]);
        }

        $view['stats'] = $view['unit']->getUnit()->getCacheStats();
        return $view;
    }
}
