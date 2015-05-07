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

class InviteRequest extends arch\component\Mail {
    
    const DESCRIPTION = 'Invite request';

    protected function _prepare($request, $message=null) {
        $this->view->setLayout('DefaultMail');
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

        $this->_prepare($request, 'Can I join your site please??');
    }
}