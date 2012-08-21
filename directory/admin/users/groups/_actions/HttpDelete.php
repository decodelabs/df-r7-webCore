<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\groups\_actions;

use df;
use df\core;
use df\arch;
use df\aura;

class HttpDelete extends arch\form\template\Delete {
    
    const ITEM_NAME = 'group';
    
    protected $_group;
    
    protected function _init() {
        $this->_group = $this->data->fetchForAction(
            'axis://user/Group',
            $this->request->query['group'],
            'delete'
        );
    }
    
    protected function _getDataId() {
        return $this->_group['id'];
    }
    
    protected function _renderItemDetails(aura\html\widget\IContainerWidget $container) {
        $container->push(
            $this->html->attributeList($this->_group)
                ->addField('name')
                ->addField('roles', function($row) {
                    return $row->roles->select()->count();
                })
                ->addField('users', function($row) {
                    return $row->users->select()->count();
                })
        );
    }
    
    protected function _deleteItem() {
        $this->_group->delete();
        $this->user->instigateGlobalKeyringRegeneration();
    }
}
