<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\cache;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpController extends arch\Controller {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function indexHtmlAction() {
        $container = $this->aura->getWidgetContainer();
        $view = $container->getView();

        $container->addMenuBar()->addLinks(
            $view->html->link(
                    $view->uri->request('~devtools/cache/purge', true),
                    $this->_('Purge all cache backends')
                )
                ->setIcon('delete')
                ->setDisposition('negative'),

            $view->html->backLink()
        );

        $container->addBlockMenu('directory://~devtools/cache/Index')
            ->shouldShowDescriptions(false);

        return $container;
    }
}