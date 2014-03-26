<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\accessErrors\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Index.html');

        $view['errorList'] = $this->data->log->accessError->select()
            ->importRelationBlock('user', 'link')
            ->paginateWith($this->request->query);

        return $view;
    }
}