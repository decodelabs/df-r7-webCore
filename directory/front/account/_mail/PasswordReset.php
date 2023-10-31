<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\account\_mail;

use DecodeLabs\R7\Legacy;
use df\arch;

class PasswordReset extends arch\mail\Base
{
    public const SUBJECT = 'Reset your password';

    public function execute()
    {
        $this->checkSlots('key');
        $this->addRecipient($this['key']['user']);
    }

    public function preparePreview()
    {
        $this['key'] = $this->data->user->passwordResetKey->newRecord([
            'key' => Legacy::hash(uniqid()),
            'user' => $this->data->user->client->fetch()->toRow(),
            'adapter' => 'Local',
            'creationDate' => 'now'
        ]);
    }
}
