<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\theme\layouts\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\fire;

use DecodeLabs\Exceptional;

class HttpDetails extends arch\node\Base
{
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml()
    {
        $view = $this->apex->view('Details.html');
        $config = fire\Config::getInstance();

        if (!$view['layout'] = $config->getLayoutDefinition($this->request['layout'])) {
            throw Exceptional::{'df/fire/layout/NotFound'}([
                'message' => 'Layout not found',
                'http' => 404
            ]);
        }

        return $view;
    }
}
