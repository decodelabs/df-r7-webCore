<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\theme;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpController extends arch\Controller {

	const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function indexHtmlAction() {
        $view = $this->aura->getWidgetContainer();
        $view->addBlockMenu('directory://~devtools/theme/Index');

        return $view;
    }
}