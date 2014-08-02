<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\tasks\logs\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpDeleteAll extends arch\form\template\Delete {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'log list';

    protected function _renderItemDetails($container) {
        $container->addAttributeList([])
            ->addField('logs', function() {
                return $this->data->task->log->select()->count();
            });
    }

    protected function _deleteItem() {
        $this->data->task->log->delete()->execute();
    }
}