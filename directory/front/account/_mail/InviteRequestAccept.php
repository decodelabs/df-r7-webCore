<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_mail;

use df\arch;

class InviteRequestAccept extends arch\mail\Base
{
    public const DESCRIPTION = 'Invite request accept notification';

    public function execute()
    {
        $this->checkSlots('request');
        $this->addToAddress($this['request']['email'], $this['request']['name']);
    }

    public function preparePreview()
    {
        $this['request'] = $this->data->user->inviteRequest->newRecord([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'companyName' => 'Test',
            'companyPosition' => 'Test monkey'
        ]);

        $this['message'] = 'Thanks for your interest in our site, your account is now active.';
    }
}
