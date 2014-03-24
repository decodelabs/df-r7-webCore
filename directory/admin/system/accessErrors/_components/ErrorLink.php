<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\accessErrors\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class ErrorLink extends arch\component\template\RecordLink {

    protected $_icon = 'lock';

// Url
    protected function _getRecordUrl($id) {
        return '~admin/system/access-errors/details?error='.$id;
    }

    protected function _getRecordName() {
        return $this->format->userDateTime($this->_record['date'], 'short');
    }
}