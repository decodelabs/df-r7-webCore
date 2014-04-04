<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\theme\icons\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpIndex extends arch\Action {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        $container = $this->aura->getWidgetContainer();
        $container->push('Coming soon...');

        return $container;
    }
}