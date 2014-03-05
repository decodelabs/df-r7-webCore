<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\invites;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpController extends arch\Controller {

    public function indexHtmlAction() {
        $view = $this->aura->getView('Index.html');

        $this->data->checkAccess($this->data->user->invite);

        $view['inviteList'] = $this->data->user->invite->select()
            ->populateSelect('groups', 'id', 'name')
            ->populateSelect('owner', 'id', 'fullName')
            ->populateSelect('user', 'id', 'fullName')
            ->paginateWith($this->request->query);

        return $view;
    }

    public function detailsHtmlAction() {
        $view = $this->aura->getView('Details.html');

        $view['invite'] = $this->data->fetchForAction(
            'axis://user/Invite',
            $this->request->query['invite']
        );

        return $view;
    }
}