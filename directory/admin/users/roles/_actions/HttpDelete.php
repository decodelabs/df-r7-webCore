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
use df\user;

class HttpDelete extends arch\form\template\DeleteRecord {
        
    const ITEM_NAME = 'role';
    const ENTITY_LOCATOR = 'axis://user/Role';
    
    protected function _loadRecord() {
        return $this->_fetchRecordForAction(
            $this->request->query['role'],
            'delete'
        );
    }
    
    
    protected function _addAttributeListFields($attributeList) {
        $attributeList

            // Name
            ->addField('name')

            // Bind state
            ->addField('bindState', $this->_('Bind state'), function($row) {
                if($row['bindState'] !== null) {
                    return user\Client::stateIdToName($row['bindState']);
                }
            })

            // Min required state
            ->addField('minRequiredState', $this->_('Minimum required state'), function($row) {
                if($row['minRequiredState'] !== null) {
                    return user\Client::stateIdToName($row['minRequiredState']);
                }
            })

            // Priority
            ->addField('priority')

            // Groups
            ->addField('groups', function($row) {
                return $row->groups->select()->count();
            })

            // Keys
            ->addField('keys', function($row) {
                return $row->keys->select()->count();
            });
    }
    
    protected function _deleteRecord() {
        $this->_record->delete();
        $this->user->instigateGlobalKeyringRegeneration();
    }
}
    