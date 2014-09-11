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

class EmailVerify extends arch\component\Mail {
    
    const DESCRIPTION = 'Email verification request';

    protected function _prepare($key, $user) {
        $this->view['key'] = $key;
        $this->view['user'] = $user;

        $this->setDefaultToAddress($user['email']);
    }

    protected function _preparePreview() {
        $user = $this->data->user->client->fetch()->toRow();
        $this->_prepare(md5(uniqid()), $user);
    }
}