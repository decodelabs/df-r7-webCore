<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpLogout extends arch\node\Base {

    const CHECK_ACCESS = false;
    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute() {
        $this->user->logout();
        return $this->http->redirect('account/login');
    }
}