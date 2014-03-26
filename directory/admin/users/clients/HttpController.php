<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\clients;

use df;
use df\core;
use df\apex;
use df\arch;
use df\user;
    
class HttpController extends arch\Controller {

    public function fetchClient($view) {
        $view['client'] = $this->data->fetchForAction(
            'axis://user/Client',
            $this->request->query['user']
        );

        return $view;
    }
}