<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_mail;

use df\arch;

class Invite extends arch\mail\Base
{
    public const DESCRIPTION = 'User invite';

    public function execute()
    {
        $this->checkSlots('invite');
        $this->addToAddress($this['invite']['email'], $this['invite']['name']);
    }

    public function preparePreview()
    {
        $this['invite'] = $this->data->user->invite->newRecord([
            'key' => md5(uniqid()),
            'owner' => $this->data->user->client->select('id')->toValue('id'),
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => '-- This is a custom message --'
        ]);
    }
}
