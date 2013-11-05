<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\invites\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class InviteLink extends arch\component\template\RecordLink {

    protected $_icon = 'mail';
    protected $_useDate = false;

    public function shouldUseDate($flag=null) {
        if($flag !== null) {
            $this->_useDate = (bool)$flag;
            return $this;
        }

        return $this->_useDate;
    }

    protected function _getRecordName() {
        return $this->_useDate ?
            $this->html->userDate($this->_record['creationDate']) :
            $this->_record['email'];
    }

    protected function _getRecordUrl($id) {
        return '~admin/users/invites/details?invite='.$id;
    }
}