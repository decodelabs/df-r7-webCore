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
        $view = $container->getView();

        $container->addMenuBar()->addLinks(
            $view->html->link(
                    $view->uri->request('~admin/navigation/refresh', true),
                    $this->_('Refresh menu list')
                )
                ->setIcon('refresh'),

            '|',

            $view->html->backLink()
        );

        $container->addBlockMenu('directory://~admin/navigation/Index');

        return $container;
    }

    public function refreshAction() {
        $this->arch->clearMenuCache();
        $this->arch->notify('complete', $this->_('The system menu list has been refreshed'), 'success');

        return $this->http->defaultRedirect();
    }
}