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
    
    protected function _loadRecord() {
        return $this->_fetchRecordForAction(
            $this->request->query['group'],
            'edit'
        );
    }
    
    protected function _setDefaultValues() {
        $this->values->name = $this->_record['name'];

        $this->getDelegate('roles')->setSelected(
            $this->_record->roles->selectFromBridge('role_id')->toList('role_id')
        );
    }
}
