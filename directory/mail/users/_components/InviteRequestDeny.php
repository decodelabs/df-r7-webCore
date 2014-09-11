<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\users\_components;

use df;
use df\core;
use df\apex;
use df\arch;

class InviteRequestDeny extends arch\component\Mail {
    
    const DESCRIPTION = 'Invite request denied notification';

    protected function _prepare($request, $message) {
        $this->view['request'] = $request;
        $this->view['message'] = $message;

        $this->setDefaultToAddress($request['email']);
    }

    protected function _preparePreview() {
        $request = $this->data->user->inviteRequest->newRecord([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'companyName' => 'Test',
            'companyPosition' => 'Test monkey'
        ]);

        $this->_prepare($request, 'Sorry, we\'re not accepting invite requests at the moment');
    }
}