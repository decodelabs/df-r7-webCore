<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\roles\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\Action {
    
    public function executeAsHtml() {
        $model = $this->data->getModel('user');
        $view = $this->aura->getView('Index.html');

        $this->data->checkAccess($model->role);
        
        $view['roleList'] = $this->data->select('id', 'name', 'bindState', 'minRequiredState', 'priority')
            ->from($model->role, 'role')
            
            ->countRelation('groups')
            ->countRelation('keys')

            ->paginateWith($this->request->query);
                
        return $view;
    }
}