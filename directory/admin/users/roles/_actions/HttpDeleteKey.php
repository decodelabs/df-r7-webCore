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
        $this->_key = $this->data->fetchForAction(
            'axis://user/Key',
            $this->request->query['key'],
            'delete'
        );
    }
    
    protected function _getDataId() {
        return $this->_key['id'];
    }
    
    protected function _renderItemDetails(aura\html\widget\IContainerWidget $container) {
        $container->push(
            $this->html->attributeList($this->_key)
                ->addField('role', function($row) {
                    return $row['role']['name'];
                })
                ->addField('domain')
                ->addField('pattern')
                ->addField('allow', $this->_('Policy'), function($row) {
                    return $row['allow'] ? $this->_('Allow') : $this->_('Deny');
                })
        );
    }
    
    protected function _deleteItem() {
        $this->_key->delete();
        $this->user->instigateGlobalKeyringRegeneration();
    }
}
