<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\shared\users\clients\_components;

use df;
use df\core;
use df\apex;
use df\arch;

class UserLink extends arch\component\RecordLink {

    protected $_icon = 'user';
    protected $_useNickName = false;
    protected $_shortenName = false;


// Name
    public function shouldUseNickName($flag=null) {
        if($flag !== null) {
            $this->_useNickName = (bool)$flag;
            return $this;
        }

        return $this->_useNickName;
    }

    public function shouldShortenName($flag=null) {
        if($flag !== null) {
            $this->_shortenName = (bool)$flag;
            return $this;
        }

        return $this->_shortenName;
    }


// Name
    protected function _getRecordName() {
        if($this->_useNickName) {
            $name = $this->_record['nickName'];
        } else {
            $name = $this->_record['fullName'];
        }

        if($this->_shortenName && preg_match('/^([^ ]+) ([^ ]+)$/', $name, $matches)) {
            $name = $matches[1].' '.ucfirst($matches[2]{0}).'.';
        }

        if($name === null) {
            $name = '#'.$this->_record['id'];
        }

        return $name;
    }

// Url
    protected function _getRecordUrl($id) {
        return '~admin/users/clients/details?user='.$id;
    }
}