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
    	$model = $this->data->getModel('mail');

    	if(!$this->user->canAccess($model->devMail, 'delete')) {
    		$this->throwError(401, 'Cannot delete dev mail');
    	}
    }

    protected function _deleteItem() {
    	$this->data->getModel('mail')->devMail->delete()->execute();
    }
}