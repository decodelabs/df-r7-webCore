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
    
    protected function _loadRecord() {
       return $this->_fetchRecordForAction(
            $this->request->query['role'],
            'edit'
        );
    }
    
    protected function _setDefaultValues() {
        $this->values->name = $this->_record['name'];
        $this->values->bindState = $this->_record['bindState'];
        $this->values->minRequiredState = $this->_record['minRequiredState'];
        $this->values->priority = $this->_record['priority'];
    }
}
