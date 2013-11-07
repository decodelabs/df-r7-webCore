<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpController extends arch\Controller {

    public function indexHtmlAction() {
        $container = $this->aura->getWidgetContainer();
        $container->push($this->directory->getComponent('IndexHeaderBar', '~admin/users/'));
        $container->addBlockMenu('directory://~admin/users/Index');

        return $container;
    }
}