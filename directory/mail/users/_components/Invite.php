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

class Invite extends arch\component\Mail {

    const DESCRIPTION = 'User invite';

    protected function _prepare($invite) {
        $this->view->setLayout('DefaultMail');
        $this->view['invite'] = $invite;
    }

    protected function _preparePreview() {
        $invite = $this->data->user->invite->newRecord([
            'key' => md5(uniqid()),
            'owner' => $this->data->user->client->select('id')->toValue('id'),
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Come and check this out!'
        ]);

        $this->_prepare($invite);
    }
}