<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\deactivations\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Index.html');

        $view['deactivationList'] = $this->data->user->clientDeactivation->select()
            ->importRelationBlock('user', 'link')
            ->paginateWith($this->request->query);

        return $view;
    }
}