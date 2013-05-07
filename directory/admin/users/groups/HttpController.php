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
                
            ->paginateWith($this->request->query);
            
            
        return $view;
    }

    public function detailsHtmlAction() {
        $view = $this->aura->getView('Details.html');
        $this->_fetchGroup($view);
        
        $view['roleList'] = $view['group']->roles->fetch()
            ->countRelation('groups')
            ->countRelation('keys')
            ->orderBy('priority');

        return $view;
    }

    public function usersHtmlAction() {
        $view = $this->aura->getView('Users.html');
        $this->_fetchGroup($view);

        $view['userList'] = $view['group']->users->select()
            ->countRelation('groups')
            ->paginateWith($this->request->query);

        return $view;
    }

    protected function _fetchGroup($view) {
        $view['group'] = $this->data->fetchForAction(
            'axis://user/Group',
            $this->request->query['group']
        );

        $view['userCount'] = $view['group']->users->select()->count();
    }
}
