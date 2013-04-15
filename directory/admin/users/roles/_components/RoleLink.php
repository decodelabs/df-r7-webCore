<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\groups\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class RoleLink extends arch\component\template\RecordLink {

    protected $_icon = 'role';

// Name
    protected function _getRecordName() {
        return $this->_record['name'];
    }

// Url
    protected function _getRecordUrl($id) {
        return '~admin/users/roles/details?role='.$id;
    }
}