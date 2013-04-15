<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users;

use df;
use df\core;
use df\apex;
use df\arch;
use df\user;
    
class HttpController extends arch\Controller {

    public function indexHtmlAction() {
        $model = $this->data->getModel('user');
        $view = $this->aura->getView('Index.html');

        $this->data->checkAccess($model->client);

        $view['userList'] = $model->client->select()

            ->countRelation('groups')

            ->paginate()
                ->setOrderableFields(
                    'email', 'fullName', 'nickName', 'status', 'joinDate',
                    'loginDate', 'timezone', 'country', 'language', 'groups'
                )
                ->setDefaultOrder('fullName')
                ->applyWith($this->request->query);

        return $view;
    }

    public function detailsHtmlAction() {
        $view = $this->aura->getView('Details.html');
        $this->_fetchUser($view);

        return $view;
    }

    public function authenticationHtmlAction() {
        $view = $this->aura->getView('Authentication.html');
        $this->_fetchUser($view);

        $view['authenticationList'] = $view['client']->authDomains->fetch()
            ->orderBy('adapter ASC');

        return $view;
    }

    protected function _fetchUser($view) {
        $view['client'] = $this->data->fetchForAction(
            'axis://user/Client',
            $this->request->query['user']
        );

        $view['authenticationCount'] = $view['client']->authDomains->select()->count();

        return $view;
    }
}