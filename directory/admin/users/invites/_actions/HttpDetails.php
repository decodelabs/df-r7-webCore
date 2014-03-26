<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\invites\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpDetails extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Details.html');

        $view['invite'] = $this->data->fetchForAction(
            'axis://user/Invite',
            $this->request->query['invite']
        );

        return $view;
    }
}