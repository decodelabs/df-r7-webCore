<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\application\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\halo;

class HttpCompile extends arch\node\ConfirmForm {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'application';

    protected function getMainMessage() {
        return $this->_('Are you sure you want to re-compile the production version of this application?');
    }

    protected function apply() {
        return $this->task->initiateStream('application/build');
    }
}