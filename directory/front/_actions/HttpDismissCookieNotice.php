<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;

class HttpDismissCookieNotice extends arch\Action {

    const OPTIMIZE = true;
    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute() {
        $theme = aura\theme\Base::factory($this->context);
        $this->http->setCookie('cnx', 1)
            ->setExpiryDate(new core\time\Date('+2 years'));

        return $this->http->defaultRedirect();
    }
}