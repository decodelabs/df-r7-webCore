<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\roles\_actions;

use df;
use df\core;
use df\arch;

class HttpEditKey extends HttpAddKey {
    
    protected function _loadRecord() {
        return $this->_fetchRecordForAction(
            $this->request->query['key'],
            'edit'
        );
    }
    
    protected function _getDataId() {
        return $this->_record['id'];
    }
    
    protected function _setDefaultValues() {
        $this->values->domain = $this->_record['domain'];
        $this->values->pattern = $this->_record['pattern'];
        $this->values->allow = $this->_record['allow'];
    }
}
