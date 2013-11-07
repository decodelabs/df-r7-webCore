<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\clients\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpEdit extends EditorBase {

    protected function _init() {
        $this->_client = $this->data->fetchForAction(
            'axis://user/Client',
            $this->request->query['user'],
            'edit'
        );
    }
    
    protected function _getDataId() {
        return $this->_client['id'];
    }
    
    protected function _setDefaultValues() {
        $this->values->importFrom($this->_client, [
            'email', 'fullName', 'nickName', 'status',
            'timezone', 'country', 'language'
        ]);
        
        $this->getDelegate('groups')->setSelected(
            $this->_client->groups->selectFromBridge('group')->toList('group')
        );
    }
}