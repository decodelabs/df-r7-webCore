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

    protected function _loadRecord() {
        return $this->_fetchRecordForAction(
            $this->request->query['user'],
            'edit'
        );
    }
    
    protected function _getDataId() {
        return $this->_record['id'];
    }
    
    protected function _setDefaultValues() {
        $this->values->email = $this->_record['email'];
        $this->values->fullName = $this->_record['fullName'];
        $this->values->nickName = $this->_record['nickName'];
        $this->values->status = $this->_record['status'];
        $this->values->timezone = $this->_record['timezone'];
        $this->values->country = $this->_record['country'];
        $this->values->language = $this->_record['language'];
        
        $this->getDelegate('groups')->setSelected(
            $this->_record->groups->selectFromBridge('group')->toList('group')
        );
    }

    protected function _saveRecord() {
        parent::_saveRecord();

        $this->data->user->auth->update([
                'identity' => $this->_record['email']
            ])
            ->where('user', '=', $this->_record)
            ->where('adapter', '=', 'Local')
            ->execute();

        $this->user->instigateGlobalKeyringRegeneration();
    }
}