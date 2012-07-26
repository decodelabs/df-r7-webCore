<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\groups\_actions;

use df;
use df\core;
use df\arch;

class HttpEdit extends HttpAdd {
    
    protected function _init() {
        $model = $this->data->getModel('user');
        
        if(!$this->user->canAccess($model->group, 'edit')) {
            $this->throwError(401, 'Cannot edit groups');
        }

        if(!$this->_group = $model->group->fetchByPrimary($this->request->query['group'])) {
            $this->throwError(404, 'Group not found');
        }
        
        // TODO: check access
    }
    
    protected function _getDataId() {
        return $this->_group['id'];
    }
    
    protected function _setDefaultValues() {
        $this->values->name = $this->_group['name'];
        $this->getDelegate('roles')->setRoleIds(
            $this->_group->roles->selectFromBridge('role_id')->toList('role_id')
        );
    }
}
