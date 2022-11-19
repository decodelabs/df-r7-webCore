<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\cookies\_nodes;

use DecodeLabs\R7\Legacy;

use df\arch;

class HttpDismissNotice extends arch\node\Base
{
    public const OPTIMIZE = true;
    public const DEFAULT_ACCESS = arch\IAccess::ALL;

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

        return Legacy::$http->defaultRedirect('/');
    }
}
