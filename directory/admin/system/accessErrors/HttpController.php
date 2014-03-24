<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\accessErrors;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpController extends arch\Controller {

    public function indexHtmlAction() {
        $view = $this->aura->getView('Index.html');

        $view['errorList'] = $this->data->log->accessError->select()
            ->importRelationBlock('user', 'link')
            ->paginateWith($this->request->query);

        return $view;
    }

    public function detailsHtmlAction() {
        $view = $this->aura->getView('Details.html');

        $view['error'] = $this->data->fetchForAction(
            'axis://log/AccessError',
            $this->request->query['error']
        );

        return $view;
    }
}