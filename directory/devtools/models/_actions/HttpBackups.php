<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpBackups extends arch\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function execute() {
        $view = $this->apex->view('Backups.html');
        $path = $this->application->getSharedStoragePath().'/backup/';
        $backups = core\io\Util::listFilesIn($path, '/^axis\-[0-9]+\.tar$/i');
        rsort($backups);
        
        $view['backupList'] = $backups;

        return $view;
    }
}