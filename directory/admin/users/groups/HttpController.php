<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\groups;

use df;
use df\core;
use df\arch;

class HttpController extends arch\Controller {
    
    public function indexHtmlAction() {
        $model = $this->data->getModel('user');
        $view = $this->aura->getView('Index.html');
        
        $view['groupList'] = $this->data->select('id', 'name')
            ->from($model->group, 'group')
            
            ->leftJoin('COUNT(groupBridge.client) as users')
                ->from($model->groupBridge, 'groupBridge')
                ->on('groupBridge.group', '=', 'group.@primary')
                ->endJoin()
                
            ->leftJoin('COUNT(roleBridge.role) as roles')
                ->from($model->group->getBridgeUnit('roles'), 'roleBridge')
                ->on('roleBridge.group', '=', 'group.@primary')
                ->endJoin()
                
            ->groupBy('group.id')
            
            ->paginate()
                ->setOrderableFields('group.name', 'users', 'roles')
                ->setDefaultOrder('group.name')
                ->setDefaultLimit(30)
                ->applyWith($this->request->query);
            
            
        return $view;
    }

    public function detailsHtmlAction() {
        $model = $this->data->getModel('user');
        $view = $this->aura->getView('Details.html');
        
        if(!$view['group'] = $model->group->fetchByPrimary($this->request->query['group'])) {
            $this->throwError(404, 'Group not found');
        }
        
        return $view;
    }
}
