<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\menus\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpDetails extends arch\action\Base {

    public function executeAsHtml() {
        $view = $this->apex->view('Details.html');

        if(!$view['menu'] = arch\navigation\menu\Base::factory($this->context, 'Directory://'.$this->request['menu'])) {
            $this->throwError(404, 'Menu not found');
        }

        $view['entryList'] = $view['menu']->generateEntries();

        return $view;
    }
}