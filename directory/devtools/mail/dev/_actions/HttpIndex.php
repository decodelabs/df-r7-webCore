<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\mail\dev\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpIndex extends arch\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Index.html');
        $model = $this->data->getModel('mail');

        $view['mailList'] = $model->devMail->fetch()
            ->paginateWith($this->request->query);

        return $view;
    }
}