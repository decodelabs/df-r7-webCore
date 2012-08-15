<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\users;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpController extends arch\Controller {

	const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function indexHtmlAction() {
    	$container = $this->aura->getWidgetContainer();

    	$container->addMenuBar()->addLinks(
    		$container->getView()->html->backLink()
		);

        $container->addBlockMenu('directory://~devtools/users/Index');

        return $container;
    }
}