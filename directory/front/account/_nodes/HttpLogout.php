<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\account\_nodes;

use DecodeLabs\R7\Legacy;

use df\arch;

class HttpLogout extends arch\node\Base
{
    public const CHECK_ACCESS = false;
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute()
    {
        $this->user->auth->unbind();
        return Legacy::$http->redirect('account/login');
    }
}
