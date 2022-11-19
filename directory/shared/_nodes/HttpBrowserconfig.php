<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\shared\_nodes;

use df\arch;

class HttpBrowserconfig extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::ALL;
    public const CHECK_ACCESS = false;

    public function executeAsXml()
    {
        $view = $this->apex->view('Browserconfig.xml');
        $theme = $this->apex->getTheme();

        $view['hasImage'] = (bool)$theme->getApplicationImagePath();
        $view['tileColor'] = $theme->getApplicationColor()->toHexString();

        return $view;
    }
}
