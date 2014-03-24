<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\criticalErrors\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpDeleteAll extends arch\form\template\Delete {

    const ITEM_NAME = 'error list';


    protected function _renderItemDetails($container) {
        $container->addAttributeList([])
            ->addField('errors', function() {
                return $this->data->log->criticalError->select()->count();
            });
    }

    protected function _deleteItem() {
        $this->data->log->criticalError->delete()->execute();
    }
}