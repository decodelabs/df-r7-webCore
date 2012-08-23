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

        $this->data->checkAccess($model->group);
        
        $view['groupList'] = $this->data->select('id', 'name')
            ->from($model->group, 'group')
            
            ->correlate('COUNT(groupBridge.client) as users')
                ->from($model->groupBridge, 'groupBridge')
                ->on('groupBridge.group', '=', 'group.@primary')
                ->endCorrelation()
                
            ->correlate('COUNT(roleBridge.role) as roles')
                ->from($model->group->getBridgeUnit('roles'), 'roleBridge')
                ->on('roleBridge.group', '=', 'group.@primary')
                ->endCorrelation()
                
            ->paginate()
                ->setOrderableFields('group.name', 'users', 'roles')
                ->setDefaultOrder('group.name')
                ->setDefaultLimit(30)
                ->applyWith($this->request->query);
            
            
        return $view;
    }

    public function detailsHtmlAction() {
        $view = $this->aura->getView('Details.html');

        $view['group'] = $this->data->fetchForAction(
            'axis://user/Group',
            $this->request->query['group']
        );

        return $view;
    }
}
