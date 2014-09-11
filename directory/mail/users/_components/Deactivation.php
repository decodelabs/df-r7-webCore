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

class Deactivation extends arch\component\Mail {
    
    const DESCRIPTION = 'User deactivation';

    protected function _prepare($deactivation) {
        $this->view['deactivation'] = $deactivation;
        $this->view['client'] = $deactivation['user'];
    }

    protected function _preparePreview() {
        $deactivation = $this->data->user->clientDeactivation->newRecord([
            'user' => $this->data->user->client->select('id')->toValue('id'),
            'date' => 'now',
            'reason' => 'I don\'t want my account any more',
            'comments' => 'These are test comments'
        ]);

        $this->_prepare($deactivation);
    }
}