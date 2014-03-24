<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\criticalErrors\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class ErrorLink extends arch\component\template\RecordLink {

    protected $_icon = 'bug';

// Url
    protected function _getRecordUrl($id) {
        return '~admin/system/critical-errors/details?error='.$id;
    }

    protected function _getRecordName() {
        return $this->format->userDateTime($this->_record['date'], 'short');
    }
}