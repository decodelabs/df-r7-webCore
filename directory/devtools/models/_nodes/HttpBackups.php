<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpBackups extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function execute() {
        $view = $this->apex->view('Backups.html');
        $dir = new core\fs\Dir($this->application->getSharedStoragePath().'/backup/');

        $backups = $dir->listFileNames(function($name) {
            return preg_match('/^axis\-[0-9]+\.tar$/i', $name);
        });

        rsort($backups);

        $view['backupList'] = $backups;

        return $view;
    }
}