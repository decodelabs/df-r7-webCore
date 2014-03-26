<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\groups\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpUsers extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Users.html');
        $this->controller->fetchGroup($view);

        $view['userList'] = $view['group']->users->select()
            ->countRelation('groups')
            ->paginateWith($this->request->query);

        return $view;
    }
}