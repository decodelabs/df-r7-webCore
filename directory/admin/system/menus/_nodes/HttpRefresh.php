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

class HttpRefresh extends arch\node\Base {

    public function executeAsHtml() {
        $this->apex->clearMenuCache();

        $this->comms->flashSuccess(
            'menu-cache.clear',
            $this->_('The system menu list has been refreshed')
        );

        return $this->http->defaultRedirect();
    }
}