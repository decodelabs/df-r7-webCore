<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\inviteRequests\_mail;

use df\arch;

class RequestNotify extends arch\mail\Base
{
    public const DESCRIPTION = 'Invite request';

    public function execute()
    {
        $this->checkSlots('request');
    }

    public function preparePreview()
    {
        $this['request'] = $this->data->user->inviteRequest->newRecord([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'companyName' => 'Test',
            'companyPosition' => 'Test monkey',
            'message' => 'Can I join your site please?'
        ]);
    }
}
