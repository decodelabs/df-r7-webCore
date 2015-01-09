<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\pestControl\errors\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpArchive extends arch\form\template\Confirm {
    
    const ITEM_NAME = 'error';
    const DISPOSITION = 'negative';

    protected $_error;

    protected function _init() {
        $this->_error = $this->data->fetchForAction(
            $this->scaffold->getRecordAdapter(),
            $this->request->query['error'],
            'edit'
        );
    }

    protected function _getDataId() {
        return $this->_error['id'];
    }

    protected function _getMainMessage($itemName) {
        return $this->_('Are you sure you want to archive this error?');
    }

    protected function _renderItemDetails($container) {
        $container->push(
            $this->apex->component('ErrorDetails')
                ->setRecord($this->_error)
        );
    }

    protected function _getMainButtonText() {
        return $this->_('Archive');
    }

    protected function _getMainButtonIcon() {
        return 'remove';
    }

    protected function _apply() {
        $this->_error->archiveDate = 'now';
        $this->_error->save();
    }
}