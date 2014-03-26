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

class HttpDetails extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Details.html');

        if(!$view['menu'] = arch\navigation\menu\Base::factory($this->_context, 'Directory://'.$this->request->query['menu'])) {
            $this->throwError(404, 'Menu not found');
        }

        $view['entryList'] = $view['menu']->generateEntries();

        return $view;
    }
}