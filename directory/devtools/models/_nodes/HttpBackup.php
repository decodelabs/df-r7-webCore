<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\axis;
use df\halo;

class HttpBackup extends arch\node\ConfirmForm {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected function getMainMessage() {
        return $this->_('Are you sure you want to back up all data? This process may take a while!');
    }

    protected function customizeMainButton($button) {
        $button->setBody($this->_('Back up'))
            ->setIcon('backup');
    }

    protected function apply() {
        return $this->task->initiateStream('axis/backup');
    }
}