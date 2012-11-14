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
        $this->values->email = $this->_client['email'];
        $this->values->fullName = $this->_client['fullName'];
        $this->values->nickName = $this->_client['nickName'];
        $this->values->status = $this->_client['status'];
        $this->values->timezone = $this->_client['timezone'];
        $this->values->country = $this->_client['country'];
        $this->values->language = $this->_client['language'];
        
        $this->getDelegate('groups')->setSelected(
            $this->_client->groups->selectFromBridge('group')->toList('group')
        );
    }

    protected function _saveRecord() {
        parent::_saveRecord();

        $this->data->user->auth->update([
                'identity' => $this->_client['email']
            ])
            ->where('user', '=', $this->_client)
            ->where('adapter', '=', 'Local')
            ->execute();

        $this->user->instigateGlobalKeyringRegeneration();
    }
}