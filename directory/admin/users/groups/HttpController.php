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
            
            ->countRelation('users')
            ->countRelation('roles')
                
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
