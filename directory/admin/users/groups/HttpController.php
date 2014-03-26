<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\groups;

use df;
use df\core;
use df\arch;

class HttpController extends arch\Controller {
    
    public function fetchGroup($view) {
        $view['group'] = $this->data->fetchForAction(
            'axis://user/Group',
            $this->request->query['group']
        );
    }
}
