<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\theme\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpIndex extends arch\Action {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function executeAsHtml() {
        $container = $this->aura->getWidgetContainer();
        $container->push($this->directory->getComponent('~devtools/theme/IndexHeaderBar'));
        $container->addBlockMenu('directory://~devtools/theme/Index');

        return $container;
    }
}