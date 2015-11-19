<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\pestControl\access\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\axis;
use df\halo;

class HttpPurge extends arch\action\ConfirmForm {

    const DISPOSITION = 'negative';

    protected function getMainMessage() {
        return $this->_('Are you sure you want to delete all old access logs?');
    }

    protected function customizeMainButton($button) {
        $button->setBody($this->_('Delete'))
            ->setIcon('delete');
    }

    protected function apply() {
        return $this->task->initiateStream('pest-control/purge-access-logs');
    }
}