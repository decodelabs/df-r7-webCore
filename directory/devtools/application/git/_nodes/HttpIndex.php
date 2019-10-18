<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\application\git\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\spur;

class HttpIndex extends arch\node\Base
{
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml()
    {
        $view = $this->apex->view('Index.html');
        $view['packageList'] = $this->data->getModel('package')->getInstalledPackageList();

        return $view;
    }
}
