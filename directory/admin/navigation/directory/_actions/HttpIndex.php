<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\navigation\directory\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\Action {
    
    public function executeAsHtml() {
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
                $list = $facetController['source']->loadNestedMenus($facetController['area']);
                return $this->directory->getComponent('MenuList', '~admin/navigation/directory/')
                    ->setCollection($list);
            });
    }
}