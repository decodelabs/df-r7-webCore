<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\cookies\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;

class HttpDismissNotice extends arch\node\Base
{
    const OPTIMIZE = true;
    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute()
    {
        $cookieNotice = $this->apex->getTheme()->getFacet('cookieNotice');

        if ($cookieNotice) {
            $this->consent->setUserData([
                'version' => 0,
                'preferences' => $cookieNotice->isCategoryEnabled('preferences'),
                'statistics' => $cookieNotice->isCategoryEnabled('statistics'),
                'marketing' => $cookieNotice->isCategoryEnabled('marketing')
            ]);
        }

        return $this->http->defaultRedirect('/');
    }
}
