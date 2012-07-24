<?php

namespace df\apex\directory\admin\users\roles;

use df\core;
use df\arch;

class HttpController extends arch\Controller {
    
    public function indexHtmlAction() {
        $model = $this->data->getModel('user');
        $view = $this->aura->getView('Index.html');
        
        $view['roleList'] = $this->data->select('id', 'name', 'state', 'priority')
            ->from($model->role, 'role')
            
            ->leftJoin('COUNT(group_roles_id) as groups')
                ->from($model->role->getBridgeUnit('groups'), 'groupBridge')
                ->on('groupBridge.role_id', '=', 'role.id')
                ->endJoin()
               
            ->leftJoin('COUNT(role_id) as keys')
                ->from($model->key, 'key')
                ->on('key.role_id', '=', 'role.id')
                ->endJoin()
                
            ->groupBy('role.id')
            
            ->paginate()
                ->setOrderableFields('role.id', 'role.name', 'role.state', 'role.priority', 'groups', 'keys')
                ->setDefaultOrder('role.name')
                ->setDefaultLimit(30)
                ->applyWith($this->request->query);
                
        return $view;
    }

    public function detailsHtmlAction() {
        $model = $this->data->getModel('user');
        $view = $this->aura->getView('Details.html');
        
        if(!$view['role'] = $model->role->fetchByPrimary($this->request->query['role'])) {
            $this->throwError(
                404, 'Role not found'
            );
        }
        
        return $view;
    }
}
