<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\accessErrors\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpDeleteAll extends arch\form\template\Delete {

    const ITEM_NAME = 'error list';


    protected function _renderItemDetails($container) {
        $container->addAttributeList([])
            ->addField('errors', function() {
                return $this->data->log->accessError->select()->count();
            });
    }

    protected function _deleteItem() {
        $this->data->log->accessError->delete()->execute();
    }
}