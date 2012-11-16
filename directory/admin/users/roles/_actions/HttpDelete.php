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

class HttpDelete extends arch\form\template\Delete {
        
    const ITEM_NAME = 'role';

    protected $_role;
    
    protected function _init() {
        $this->_role = $this->data->fetchForAction(
            'axis://user/Role',
            $this->request->query['role'],
            'delete'
        );
    }

    protected function _getDataId() {
        return $this->_role['id'];
    }
    
    protected function _renderItemDetails($container) {
        $container->addAttributeList($this->_role)

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
    
    protected function _deleteItem() {
        $this->_role->delete();
        $this->user->instigateGlobalKeyringRegeneration();
    }
}
    