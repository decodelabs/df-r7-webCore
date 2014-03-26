<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\Action {
    
    public function executeAsHtml() {
        $container = $this->aura->getWidgetContainer();
        $container->push($this->directory->getComponent('IndexHeaderBar', '~admin/users/'));
        $container->addBlockMenu('directory://~admin/users/Index');

        return $container;
    }
}