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

class HttpIndex extends arch\Action {
    
    public function executeAsHtml() {
        $model = $this->data->getModel('user');
        $view = $this->aura->getView('Index.html');

        $this->data->checkAccess($model->group);
        
        $view['groupList'] = $this->data->select('id', 'name')
            ->from($model->group, 'group')
            
            ->countRelation('users')
            ->countRelation('roles')
                
            ->paginateWith($this->request->query);
            
            
        return $view;
    }
}