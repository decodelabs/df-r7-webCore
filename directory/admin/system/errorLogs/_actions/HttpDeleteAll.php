<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\errorLogs\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpDeleteAll extends arch\form\template\Delete {

    const ITEM_NAME = 'log list';


    protected function _renderItemDetails($container) {
        $container->addAttributeList([])
            ->addField('logs', function() {
                return $this->data->error->log->select()->count();
            });
    }

    protected function _deleteItem() {
        $this->data->error->log->delete()->execute();
    }
}