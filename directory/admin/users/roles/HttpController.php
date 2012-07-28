<?php

namespace df\apex\directory\admin\users\roles;

use df\core;
use df\arch;

class HttpController extends arch\Controller {
    
    public function indexHtmlAction() {
        $model = $this->data->getModel('user');
        $view = $this->aura->getView('Index.html');
        
        $view['roleList'] = $this->data->select('id', 'name', 'bindState', 'minRequiredState', 'priority')
            ->from($model->role, 'role')
            
            ->correlate('COUNT(groupBridge.group) as groups')
                ->from($model->role->getBridgeUnit('groups'), 'groupBridge')
                ->on('groupBridge.role', '=', 'role.@primary')
                ->endCorrelation()

            ->correlate('COUNT(key.role) as keys')
                ->from($model->key, 'key')
                ->on('key.role', '=', 'role.@primary')
                ->endCorrelation()

            ->groupBy('role.id', 'role.name', 'role.bindState', 'role.minRequiredState', 'role.priority')
            
            ->paginate()
                ->setOrderableFields('role.id', 'role.name', 'role.bindState', 'role.minRequiredState', 'role.priority', 'groups', 'keys')
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
