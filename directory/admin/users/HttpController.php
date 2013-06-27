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

        $query = $model->client->select()
            ->countRelation('groups');


        if($view['search'] = $search = $this->request->getQueryTerm('search')) {
            $query->beginWhereClause()
                ->where('id', '=', ltrim($search, '#'))
                ->orWhere('fullName', 'contains', $search)
                ->orWhere('nickName', 'contains', $search)
                ->orWhere('email', 'contains', $search)
                ->endClause();
        }

        $view['userList'] = $query->paginateWith($this->request->query);

        return $view;
    }

    public function detailsHtmlAction() {
        $view = $this->aura->getView('Details.html');
        $this->fetchClient($view);

        return $view;
    }

    public function authenticationHtmlAction() {
        $view = $this->aura->getView('Authentication.html');
        $this->fetchClient($view);

        $view['authenticationList'] = $view['client']->authDomains->fetch()
            ->orderBy('adapter ASC');

        return $view;
    }

    public function fetchClient($view) {
        $view['client'] = $this->data->fetchForAction(
            'axis://user/Client',
            $this->request->query['user']
        );

        return $view;
    }
}