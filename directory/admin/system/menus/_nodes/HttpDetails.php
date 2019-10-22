<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\menus\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpDetails extends arch\node\Base
{
    public function executeAsHtml()
    {
        $view = $this->apex->view('Details.html');
        $view['menu'] = arch\navigation\menu\Base::factory($this->context, 'Directory://'.$this->request['menu']);

        $view['entryList'] = $view['menu']->generateEntries();

        return $view;
    }
}
