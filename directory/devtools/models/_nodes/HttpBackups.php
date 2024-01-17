<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\models\_nodes;

use DecodeLabs\Atlas;

use DecodeLabs\Genesis;
use df\arch;

class HttpBackups extends arch\node\Base
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function execute()
    {
        $view = $this->apex->view('Backups.html');

        $backups = Atlas::listFileNames(Genesis::$hub->getSharedDataPath() . '/backup/', function ($name) {
            return preg_match('/-([0-9]{14})\.sql$/i', (string)$name);
        });

        rsort($backups);

        $view['backupList'] = $backups;

        return $view;
    }
}
