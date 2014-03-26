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

class HttpDetails extends arch\Action {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Details.html');

        $view['mail'] = $this->data->fetchForAction(
            'axis://mail/DevMail',
            $this->request->query['mail']
        );

        return $view;
    }
}