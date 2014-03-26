<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\clients\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\Action {
    
    public function executeAsHtml() {
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
}