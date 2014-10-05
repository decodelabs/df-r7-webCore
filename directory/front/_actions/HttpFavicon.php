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
    
class HttpFavicon extends arch\Action {

    const OPTIMIZE = true;
    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function executeAsIco() {
        return $this->http->fileResponse($this->findFile('apex/themes/shared/assets/favicon.ico'));
    }
}