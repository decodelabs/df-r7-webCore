<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\pestControl\errors\logs\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpArchive extends arch\form\template\Confirm {
    
    const ITEM_NAME = 'log';

    protected $_log;

    protected function _init() {
        $this->_log = $this->data->fetchForAction(
            $this->scaffold->getRecordAdapter(),
            $this->request->query['log'],
            'edit'
        );
    }

    protected function _getDataId() {
        return $this->_log['id'];
    }

    protected function _getMainMessage($itemName) {
        return $this->_('Are you sure you want to archive this log?');
    }

    protected function _renderItemDetails($container) {
        $container->push(
            $this->apex->component('LogDetails')
                ->setRecord($this->_log)
        );
    }

    protected function _getMainButtonText() {
        return $this->_('Archive');
    }

    protected function _getMainButtonIcon() {
        return 'save';
    }

    protected function _apply() {
        $this->_log->isArchived = true;
        $this->_log->save();
    }
}