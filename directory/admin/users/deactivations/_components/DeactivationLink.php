<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\deactivations\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class DeactivationLink extends arch\component\template\RecordLink {

    protected $_icon = 'remove';

// Url
    protected function _getRecordUrl($id) {
        return '~admin/users/deactivations/details?deactivation='.$id;
    }

    protected function _getRecordName() {
        return $this->html->userDate($this->_record['date']);
    }
}