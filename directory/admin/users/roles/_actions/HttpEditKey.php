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
        $model = $this->data->getModel('user');
        
        if(!$this->_key = $model->key->fetchByPrimary($this->request->query['key'])) {
            $this->throwError(404, 'Key not found');
        }
        
        // TODO: check access
    }
    
    protected function _getDataId() {
        return $this->_key['id'];
    }
    
    protected function _setDefaultValues() {
        $this->values->domain = $this->_key['domain'];
        $this->values->pattern = $this->_key['pattern'];
        $this->values->allow = $this->_key['allow'];
    }
}
