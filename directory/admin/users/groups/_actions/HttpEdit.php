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
        $this->_group = $this->data->fetchForAction(
            'axis://user/Group',
            $this->request->query['group'],
            'edit'
        );
    }

    protected function _getDataId() {
        return $this->_group['id'];
    }
    
    protected function _setDefaultValues() {
        $this->values->importFrom($this->_group, [
            'name'
        ]);

        $this->getDelegate('roles')->setSelected(
            $this->_group->roles->selectFromBridge('role_id')->toList('role_id')
        );
    }
}
