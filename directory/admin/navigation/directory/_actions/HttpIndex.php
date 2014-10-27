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
        $view = $this->aura->getView('Index.html');
        $source = arch\navigation\menu\source\Base::factory($this->context, 'directory');
        $area = trim($this->request->query->get('area', 'admin'), ':~');

        if(empty($area)) {
            $area = null;
        }

        $view['areaFilter'] = $area;
        $view['areaList'] = [$this->_('- All -')] + $source->getAreaOptionList();
        $view['menuList'] = $source->loadNestedMenus($area);

        return $view;
    }
}