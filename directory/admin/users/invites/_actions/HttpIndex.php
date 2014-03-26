<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\invites\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Index.html');

        $this->data->checkAccess($this->data->user->invite);

        $view['inviteList'] = $this->data->user->invite->select()
            ->populateSelect('groups', 'id', 'name')
            ->importRelationBlock('owner', 'link')
            ->importRelationBlock('user', 'link')
            ->paginateWith($this->request->query);

        return $view;
    }
}