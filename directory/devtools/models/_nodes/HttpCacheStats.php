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

class HttpCacheStats extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        $view = $this->apex->view('CacheStats.html');
        $this->controller->fetchUnit($view, 'cache');

        $view['stats'] = $view['unit']->getUnit()->getCacheStats();
        return $view;
    }
}