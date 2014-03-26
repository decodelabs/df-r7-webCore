<?php

namespace df\apex\directory\admin\users\roles;

use df\core;
use df\arch;

class HttpController extends arch\Controller {
    
    public function fetchRole($view) {
        $view['role'] = $this->data->fetchForAction(
            'axis://user/Role',
            $this->request->query['role']
        );

        return $view;
    }
}
