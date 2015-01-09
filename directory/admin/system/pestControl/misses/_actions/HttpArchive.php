<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\pestControl\misses\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpArchive extends arch\form\template\Confirm {
    
    const ITEM_NAME = 'error';
    const DISPOSITION = 'negative';

    protected $_miss;

    protected function _init() {
        $this->_miss = $this->data->fetchForAction(
            $this->scaffold->getRecordAdapter(),
            $this->request->query['miss'],
            'edit'
        );
    }

    protected function _getDataId() {
        return $this->_miss['id'];
    }

    protected function _getMainMessage($itemName) {
        return $this->_('Are you sure you want to archive this error?');
    }

    protected function _renderItemDetails($container) {
        $container->push(
            $this->apex->component('MissDetails')
                ->setRecord($this->_miss)
        );
    }

    protected function _getMainButtonText() {
        return $this->_('Archive');
    }

    protected function _getMainButtonIcon() {
        return 'remove';
    }

    protected function _apply() {
        $this->_miss->archiveDate = 'now';
        $this->_miss->save();
    }
}