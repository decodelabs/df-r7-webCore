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

class EmailVerify extends arch\mail\Base {

    const SUBJECT = 'Please verify your email address';

    public function execute() {
        $this->checkSlots('key', 'user');
        $this->addRecipient($this['user']);
    }

    public function preparePreview() {
        $this['user'] = $this->data->user->client->fetch()->toRow();
        $this['key'] = md5(uniqid());
    }
}