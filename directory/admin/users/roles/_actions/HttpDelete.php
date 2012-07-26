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
        $model = $this->data->getModel('user');
        
        if(!$this->user->canAccess($model->key, 'delete')) {
            $this->throwError(401, 'Cannot delete roles');
        }

        if(!$this->_role = $model->role->fetchByPrimary($this->request->query['role'])) {
            $this->throwError(404, 'Role not found');
        }
    }
    
    protected function _getDataId() {
        return $this->_role['id'];
    }
    
    protected function _renderItemDetails(aura\html\widget\IContainerWidget $container) {
        $container->push(
            $this->html->attributeList($this->_role)
                ->addField('name')
                ->addField('bindState', $this->_('Bind state'), function($row, $view) {
                    if($row['bindState'] !== null) {
                        return user\Client::stateIdToName($row['bindState']);
                    }
                })
                ->addField('minRequiredState', $this->_('Minimum required state'), function($row, $view) {
                    if($row['minRequiredState'] !== null) {
                        return user\Client::stateIdToName($row['minRequiredState']);
                    }
                })
                ->addField('priority')
                ->addField('groups', function($row) {
                    return $row->groups->select()->count();
                })
                ->addField('keys', function($row) {
                    return $row->keys->select()->count();
                })
        );
    }
    
    protected function _deleteItem() {
        $this->_role->delete();
        $this->user->instigateGlobalKeyringRegeneration();
    }
}
    