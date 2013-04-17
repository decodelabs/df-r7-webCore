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
    
class HttpDelete extends arch\form\template\Delete {

    const ITEM_NAME = 'user';

    protected $_user;

    protected function _init() {
        $this->_user = $this->data->fetchForAction(
            'axis://user/Client',
            $this->request->query['user'],
            'delete'
        );
    }

    protected function _getDataId() {
        return $this->_user['id'];
    }

    protected function _renderItemDetails($container) {
        $container->addAttributeList($this->_user)
            ->addField('fullName')
            ->addField('nickName')
            ->addField('email', function($row) {
                return $this->html->mailLink($row['email']);
            })
            
            ->addField('status', function($row) {
                return $this->user->client->stateIdToName($row['status']);
            })
            
            ->addField('country', function($row) {
                return $this->i18n->countries->getName($row['country']);
            })
            ;
    }

    protected function _deleteItem() {
        $model = $this->data->getModel('user');

        $model->groupBridge->delete()
            ->where('client', '=', $this->_user)
            ->execute();

        $model->auth->delete()
            ->where('user', '=', $this->_user)
            ->execute();

        $this->_user->delete();
    }
}