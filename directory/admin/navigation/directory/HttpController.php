<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\navigation\directory;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpController extends arch\Controller {

    public function indexHtmlAction() {
        $this->navigation->getBreadcrumbs();

        return $this->directory->newFacetController()
            // Init
            ->setInitializer(function($facetController) {
                $area = trim($this->request->query->get('area', 'admin'), ':~');

                if(empty($area)) {
                    $area = null;
                }

                $facetController['source'] = arch\navigation\menu\source\Base::factory($this->_context, 'directory');
                $facetController['area'] = $area;
            })

            // Main
            ->setAction(function($facetController) {
                $view = $this->aura->getView('Index.html');

                $view['facetController'] = $facetController;
                $view['areaFilter'] = $facetController['area'];
                $view['areaList'] = [$this->_('- All -')] + $facetController['source']->getAreaOptionList();

                return $view;
            })
            
            // List
            ->addFacet('list', function($facetController) {
                $template = $this->aura->getDirectoryTemplate('elements/List.html');
                $template['menuList'] = $facetController['source']->loadNestedMenus($facetController['area']);

                return $template;
            });
    }

    public function detailsHtmlAction() {
        $view = $this->aura->getView('Details.html');
        $this->_fetchMenu($view);

        return $view;
    }

    public function entriesHtmlAction() {
        $view = $this->aura->getView('Entries.html');
        $this->_fetchMenu($view);
        
        $view['entryList'] = $view['menu']->generateEntries();

        return $view;
    }

    protected function _fetchMenu($view) {
        if(!$view['menu'] = arch\navigation\menu\Base::factory($this->_context, 'Directory://'.$this->request->query['menu'])) {
            $this->throwError(404, 'Menu not found');
        }

        return $view;
    }
}