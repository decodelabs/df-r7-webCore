<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpController extends arch\Controller {

    public function indexHtmlAction() {
        $container = $this->aura->getWidgetContainer();
        $container->addBlockMenu('directory://~admin/Index');

        return $container;
    }
}