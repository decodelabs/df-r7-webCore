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

            ->paginateWith($this->request->query);
                
        return $view;
    }

    public function detailsHtmlAction() {
        $view = $this->aura->getView('Details.html');
        $this->_fetchRole($view);

        $view['keyList'] = $view['role']->keys->fetch()
            ->orderBy('domain');
        
        return $view;
    }

    protected function _fetchRole($view) {
        $view['role'] = $this->data->fetchForAction(
            'axis://user/Role',
            $this->request->query['role']
        );

        return $view;
    }
}
