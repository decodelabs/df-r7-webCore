<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\roles\_actions;

use df;
use df\core;
use df\arch;
use df\aura;

class HttpDeleteKey extends arch\form\template\Delete {
    
    const ITEM_NAME = 'key';
    
    protected $_key;
    
    protected function _init() {
        $model = $this->data->getModel('user');
        
        if(!$this->_key = $model->key->fetchByPrimary($this->request->query['key'])) {
            $this->throwError(404, 'Key not found');
        }
        
        // TODO: check access
    }
    
    protected function _getDataId() {
        return $this->_key['id'];
    }
    
    protected function _renderItemDetails(aura\html\widget\IContainerWidget $container) {
        $container->push(
            $this->html->attributeList($this->_key)
                ->addField('role', function($row, $view) {
                    return $row['role']['name'];
                })
                ->addField('domain')
                ->addField('pattern')
                ->addField('allow', $this->_('Policy'), function($row, $view) {
                    return $row['allow'] ? $view->_('Allow') : $view->_('Deny');
                })
        );
    }
    
    protected function _deleteItem() {
        $this->_key->delete();
    }
}
