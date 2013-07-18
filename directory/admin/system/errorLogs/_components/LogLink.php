<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\errorLogs\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class LogLink extends arch\component\template\RecordLink {

    protected $_icon = 'log';

// Url
    protected function _getRecordUrl($id) {
        return '~admin/system/error-logs/details?log='.$id;
    }

    protected function _getRecordName() {
        return $this->format->userDateTime($this->_record['date'], 'short');
    }
}