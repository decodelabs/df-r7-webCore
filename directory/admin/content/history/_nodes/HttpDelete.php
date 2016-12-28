<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\history\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpDelete extends arch\node\DeleteForm {

    const ITEM_NAME = 'history event';

    protected $_history;

    protected function init() {
        $this->_history = $this->data->fetchForAction(
            'axis://content/History',
            $this->request['history']
        );
    }

    protected function getInstanceId() {
        return $this->_history['id'];
    }

    protected function createItemUi($container) {
        $container->addAttributeList($this->_history)
            // User
            ->addField('user', function($history) {
                return $this->apex->component('~admin/users/clients/UserLink', $history['user']);
            })

            // Entity
            ->addField('entity')

            // Timestamp
            ->addField('timestamp', $this->_('At'), function($history) {
                return $this->html->timeSince($history['date']);
            })

            // Description
            ->addField('description', function($history) {
                return $this->html->simpleTags($history['description']);
            });
    }

    protected function apply() {
        $this->_history->delete();
    }
}