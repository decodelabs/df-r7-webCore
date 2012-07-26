<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpEdit extends HttpAdd {

    protected function _init() {
        $model = $this->data->getModel('user');

        if(!$this->user->canAccess($model->client, 'edit')) {
            $this->throwError(401, 'Cannot edit clients');
        }
        
        if(!$this->_client = $model->client->fetchByPrimary($this->request->query['user'])) {
            $this->throwError(404, 'User not found');
        }
    }
    
    protected function _getDataId() {
        return $this->_client['id'];
    }
    
    protected function _setDefaultValues() {
        $this->values->email = $this->_client['email'];
        $this->values->fullName = $this->_client['fullName'];
        $this->values->nickName = $this->_client['nickName'];
        $this->values->status = $this->_client['status'];
        $this->values->timezone = $this->_client['timezone'];
        $this->values->country = $this->_client['country'];
        $this->values->language = $this->_client['language'];
        
        $this->getDelegate('groups')->setGroupIds(
            $this->_client->groups->selectFromBridge('group_id')->toList('group_id')
        );
    }
}