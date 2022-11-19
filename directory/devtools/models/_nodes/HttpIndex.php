<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_nodes;

use df\arch;
use df\axis;

class HttpIndex extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml()
    {
        $view = $this->apex->view('Index.html');
        $probe = new axis\introspector\Probe();
        $view['unitList'] = $probe->probeUnits();

        return $view;
    }
}
