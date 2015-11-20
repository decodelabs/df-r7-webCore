<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\pestControl\errors\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\axis;
use df\halo;

class HttpPurge extends arch\node\ConfirmForm {

    const DISPOSITION = 'negative';

    protected function getMainMessage() {
        return $this->_('Are you sure you want to delete all old unarchived error logs?');
    }

    protected function customizeMainButton($button) {
        $button->setBody($this->_('Delete'))
            ->setIcon('delete');
    }

    protected function apply() {
        return $this->task->initiateStream('pest-control/purge-error-logs');
    }
}