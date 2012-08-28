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

class HttpDeleteKey extends arch\form\template\DeleteRecord {
    
    const ITEM_NAME = 'key';
    const ENTITY_LOCATOR = 'axis://user/Key';
    
    protected function _loadRecord() {
        return $this->_fetchRecordForAction(
            $this->request->query['key'],
            'delete'
        );
    }
    
    protected function _addAttributeListFields($attributeList) {
        $attributeList

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
        $this->_record->delete();
        $this->user->instigateGlobalKeyringRegeneration();
    }
}
