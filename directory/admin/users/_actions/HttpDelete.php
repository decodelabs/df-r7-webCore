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
    
class HttpDelete extends arch\form\template\DeleteRecord {

    const ITEM_NAME = 'user';
    const ENTITY_LOCATOR = 'axis://user/Client';

    protected function _loadRecord() {
        return $this->_fetchRecordForAction(
            $this->request->query['user'],
            'delete'
        );
    }

    protected function _addAttributeListFields($attributeList) {
        $attributeList
            ->addField('fullName')
            ->addField('nickName')
            ->addField('email', function($row) {
                return $this->html->link($this->view->uri->mailto($row['email']), $row['email'])
                    ->setIcon('mail')
                    ->setDisposition('transitive');
            })
            
            ->addField('status', function($row) {
                return $this->user->client->stateIdToName($row['status']);
            })
            
            ->addField('country', function($row) {
                return $this->i18n->countries->getName($row['country']);
            })
            ;
    }

    protected function _deleteRecord() {
        $model = $this->data->getModel('user');

        $model->groupBridge->delete()
            ->where('client', '=', $this->_record)
            ->execute();

        $model->auth->delete()
            ->where('user', '=', $this->_record)
            ->execute();

        $this->_record->delete();
    }
}