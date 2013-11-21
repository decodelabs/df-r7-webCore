<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\mail\dev;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpController extends arch\Controller {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    public function indexHtmlAction() {
        $view = $this->aura->getView('Index.html');
        $model = $this->data->getModel('mail');

        $view['mailList'] = $model->devMail->fetch()
            ->paginateWith($this->request->query);

        return $view;
    }

    public function detailsHtmlAction() {
        $view = $this->aura->getView('Details.html');

        $view['mail'] = $this->data->fetchForAction(
            'axis://mail/DevMail',
            $this->request->query['mail']
        );

        return $view;
    }

    public function messageHtmlAction() {
        $view = $this->aura->getView('Message.html');

        $view['mail'] = $this->data->fetchForAction(
            'axis://mail/DevMail',
            $this->request->query['mail']
        );

        $view['message'] = $view['mail']->toMessage();
        $view->setLayout('Blank');
        
        return $view;
    }
}