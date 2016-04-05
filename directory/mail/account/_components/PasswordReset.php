<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\account\_components;

use df;
use df\core;
use df\apex;
use df\arch;

class PasswordReset extends arch\component\Mail {

    const DESCRIPTION = 'Password reset key';

    protected function _prepare($key) {
        $this->view->setLayout('DefaultMail');
        $this->view['key'] = $key;

        $this->setDefaultToAddress($key['user']['email']);
    }

    protected function _preparePreview() {
        $key = $this->data->user->passwordResetKey->newRecord([
            'key' => $this->data->hash(uniqid()),
            'user' => $this->data->user->client->fetch()->toRow(),
            'adapter' => 'Local',
            'creationDate' => 'now'
        ]);

        $this->_prepare($key);
    }
}