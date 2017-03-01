<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\theme\layouts;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;
use df\fire;

class HttpController extends arch\Controller {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function fetchLayout($view) {
        $config = fire\Config::getInstance();

        if(!$view['layout'] = $config->getLayoutDefinition($this->request['layout'])) {
            throw core\Error::{'fire/layout/ENotFound'}([
                'message' => 'Layout not found',
                'http' => 404
            ]);
        }

        return $view;
    }
}