<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\mail\dev\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;
    
class HttpDeleteAll extends arch\form\template\Delete {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'mailbox';

    protected function _init() {
        $this->data->checkAccess('axis://mail/DevMail', 'delete');
    }

    protected function _deleteItem() {
    	$this->data->getModel('mail')->devMail->delete()->execute();
    }
}