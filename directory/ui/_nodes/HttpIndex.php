<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\ui\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\node\Base {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        if($this->application->isProduction()) {
            $this->throwError(401, 'Dev mode only');
        }

        $view = $this->apex->view('__Index.html');
        $files = df\Launchpad::$loader->lookupFileListRecursive('apex/directory/ui/_templates', 'php');
        unset($files['__Index.html.php']);
        $view['files'] = $files;

        return $view;
    }
}