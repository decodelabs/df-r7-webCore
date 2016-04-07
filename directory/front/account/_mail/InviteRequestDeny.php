<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_mail;

use df;
use df\core;
use df\apex;
use df\arch;

class InviteRequestDeny extends arch\mail\Base {

    const DESCRIPTION = 'Invite request denied notification';

    public function execute() {
        $this->checkSlots('request');
        $this->addToAddress($this['request']['email'], $this['request']['name']);
    }

    public function preparePreview() {
        $this['request'] = $this->data->user->inviteRequest->newRecord([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'companyName' => 'Test',
            'companyPosition' => 'Test monkey'
        ]);

        $this['message'] = 'I\'m afraid we\'re not accepting invite requests at the moment';
    }
}