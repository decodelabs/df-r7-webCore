<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\groups\_actions;

use df;
use df\core;
use df\arch;
use df\aura;

class HttpDelete extends arch\form\template\DeleteRecord {
    
    const ITEM_NAME = 'group';
    const ENTITY_LOCATOR = 'axis://user/Group';
    
    protected function _loadRecord() {
        return $this->_fetchRecordForAction(
            $this->request->query['group'],
            'delete'
        );
    }
    
    protected function _addAttributeListFields($attributeList) {
        $attributeList
            ->addField('name')
            ->addField('roles', function($row) {
                return $row->roles->select()->count();
            })
            ->addField('users', function($row) {
                return $row->users->select()->count();
            })
            ;
    }
    
    protected function _deleteRecord() {
        $this->_record->delete();
        $this->user->instigateGlobalKeyringRegeneration();
    }
}
