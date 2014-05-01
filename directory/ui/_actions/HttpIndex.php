<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\ui\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        if($this->application->isProduction()) {
            $this->throwError(401, 'Dev mode only');
        }

        $view = $this->aura->getView('__Index.html');
        $files = df\Launchpad::$loader->lookupFileListRecursive('apex/directory/ui/_templates', 'php');
        unset($files['__Index.html.php']);
        $view['files'] = $files;

        return $view;
    }
}