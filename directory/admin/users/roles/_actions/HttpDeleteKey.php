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

    protected function init() {
        $this->_key = $this->data->fetchForAction(
            'axis://user/Key',
            $this->request->query['key'],
            'delete'
        );
    }

    protected function getInstanceId() {
        return $this->_key['id'];
    }
    
    protected function createItemUi($container) {
        $container->addAttributeList($this->_key)

            // Role
            ->addField('role', function($row) {
                return $row['role']['name'];
            })

            // Domain
            ->addField('domain')

            // Pattern
            ->addField('pattern')

            // Allow
            ->addField('allow', $this->_('Policy'), function($row) {
                return $row['allow'] ? $this->_('Allow') : $this->_('Deny');
            });
    }
    
    protected function _deleteRecord() {
        $this->_key->delete();
        $this->user->instigateGlobalKeyringRegeneration();
    }
}
