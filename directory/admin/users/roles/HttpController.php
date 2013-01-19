<?php

namespace df\apex\directory\admin\users\roles;

use df\core;
use df\arch;

class HttpController extends arch\Controller {
    
    public function indexHtmlAction() {
        $model = $this->data->getModel('user');
        $view = $this->aura->getView('Index.html');

        $this->data->checkAccess($model->role);
        
        $view['roleList'] = $this->data->select('id', 'name', 'bindState', 'minRequiredState', 'priority')
            ->from($model->role, 'role')
            
            ->countRelation('groups')
            ->countRelation('keys')

            ->groupBy('role.id', 'role.name', 'role.bindState', 'role.minRequiredState', 'role.priority')
            
            ->paginate()
                ->setOrderableFields('role.id', 'role.name', 'role.bindState', 'role.minRequiredState', 'role.priority', 'groups', 'keys')
                ->setDefaultOrder('role.name')
                ->setDefaultLimit(30)
                ->applyWith($this->request->query);
                
        return $view;
    }

    public function detailsHtmlAction() {
        $view = $this->aura->getView('Details.html');

        $view['role'] = $this->data->fetchForAction(
            'axis://user/Role',
            $this->request->query['role']
        );
        
        return $view;
    }
}
