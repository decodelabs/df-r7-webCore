<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\deactivations\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpDelete extends arch\form\template\Delete {

    const ITEM_NAME = 'deactivation record';

    protected $_deactivation;

    protected function _init() {
        $this->_deactivation = $this->data->fetchForAction(
            'axis://user/ClientDeactivation',
            $this->request->query['deactivation'],
            'delete'
        );
    }

    protected function _getDataId() {
        return $this->_deactivation['id'];
    }

    protected function _renderItemDetails($container) {
        $container->addAttributeList($this->_deactivation)
            ->addField('user', function($deactivation) {
                return $this->import->component('UserLink', '~admin/users/clients/', $deactivation['user']);
            })
            ->addField('date', function($deactivation) {
                return $this->html->userDate($deactivation['date']);
            })
            ->addField('reason')
            ->addField('comments', function($deactivation) {
                return $this->html->plainText($deactivation['comments']);
            });
    }

    protected function _deleteItem() {
        $this->_deactivation->delete();
    }
}