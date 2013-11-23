<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\deactivations;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpController extends arch\Controller {

    public function indexHtmlAction() {
        $view = $this->aura->getView('Index.html');

        $view['deactivationList'] = $this->data->user->clientDeactivation->select()
            ->populate('user')
            ->paginateWith($this->request->query);

        return $view;
    }

    public function detailsHtmlAction() {
        $view = $this->aura->getView('Details.html');

        $view['deactivation'] = $this->data->fetchForAction(
            'axis://user/ClientDeactivation',
            $this->request->query['deactivation']
        );

        return $view;
    }
}