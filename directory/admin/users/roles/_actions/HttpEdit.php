<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\roles\_actions;

use df;
use df\core;
use df\arch;

class HttpEdit extends HttpAdd {
    
    protected function _init() {
        $model = $this->data->getModel('user');
        
        if(!$this->_role = $model->role->fetchByPrimary($this->request->query['role'])) {
            $this->throwError(404, 'Role not found');
        }
        
        // TODO: check access
    }
    
    protected function _getDataId() {
        return $this->_role['id'];
    }
    
    protected function _setDefaultValues() {
        $this->values->name = $this->_role['name'];
        $this->values->state = $this->_role['state'];
        $this->values->priority = $this->_role['priority'];
    }
}
