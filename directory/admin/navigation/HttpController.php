<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\navigation;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpController extends arch\Controller {

    public function indexHtmlAction() {
        $container = $this->aura->getWidgetContainer();
        $container->push($this->directory->getComponent('IndexHeaderBar', '~admin/navigation/'));
        $container->addBlockMenu('directory://~admin/navigation/Index');

        return $container;
    }

    public function refreshAction() {
        $this->navigation->clearMenuCache();

        $this->comms->flash(
            'menu-cache.clear', 
            $this->_('The system menu list has been refreshed'), 
            'success'
        );

        return $this->http->defaultRedirect();
    }
}