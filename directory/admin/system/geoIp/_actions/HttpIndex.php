<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\geoIp\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;
use df\link;

class HttpIndex extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->apex->view('Index.html');
        $handler = link\geoIp\Handler::factory();

        $view['config'] = link\geoIp\Config::getInstance();
        $view['result'] = $handler->lookup($this->http->getIp());
        $view['adapterList'] = link\geoIp\Handler::getAdapterList();

        return $view;
    }
}