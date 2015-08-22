<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\capture\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;
    
class HttpDeleteAll extends arch\form\template\Delete {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'mailbox';

    protected function init() {
        $this->data->checkAccess('axis://mail/Capture', 'delete');
    }

    protected function apply() {
        $this->data->getModel('mail')->capture->delete()->execute();
    }
}