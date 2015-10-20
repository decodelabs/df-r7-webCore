<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpJoinSession extends arch\Action {

    const DEFAULT_ACCESS = arch\IAccess::ALL;

    public function execute() {
        if(isset($this->request['401']) && !$this->user->isLoggedIn()) {
            $request = arch\Request::factory('account/login');
            $request->query->rf = $this->request->encode();
            return $this->http->redirect($request);
        }

        $key = $this->data->fetchForAction(
            'axis://session/Stub',
            hex2bin($this->request['key'])
        );

        if($key['date']->lt('-1 minute')) {
            $this->throwError(500, 'Old stub');
        }

        if(!$key['sessionId']) {
            $key['sessionId'] = $this->user->session->getDescriptor()->internalId;
            $key->save();
        }

        return $this->http->defaultRedirect();
    }
}