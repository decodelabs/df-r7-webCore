<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\deactivations\_mail;

use df;
use df\core;
use df\apex;
use df\arch;

class DeactivationNotify extends arch\mail\Base {

    const DESCRIPTION = 'User deactivation';

    public function execute() {
        $this->checkSlots('deactivation');
        $this['user'] = $this['deactivation']['user'];
    }

    public function preparePreview() {
        $this['deactivation'] = $this->data->user->clientDeactivation->newRecord([
            'user' => $this->data->user->client->select('id')->toValue('id'),
            'date' => 'now',
            'reason' => 'I don\'t want my account any more',
            'comments' => 'These are test comments'
        ]);
    }
}