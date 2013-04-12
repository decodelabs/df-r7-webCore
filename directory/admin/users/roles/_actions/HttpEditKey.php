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
    
    protected function _init() {
        $this->_key = $this->data->fetchForAction(
            'axis://user/Key',
            $this->request->query['key'],
            'edit'
        );

        $this->_role = $this->_key['role'];
    }
    
    protected function _getDataId() {
        return $this->_key['id'];
    }
    
    protected function _setDefaultValues() {
        $this->values->importFrom($this->_key, [
            'domain', 'pattern', 'allow'
        ]);
    }
}
