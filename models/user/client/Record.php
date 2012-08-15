<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\user\client;

use df;
use df\core;
use df\axis;
use df\opal;
use df\user;

class Record extends opal\query\record\Base implements user\IActiveClientDataObject {
    
    public function getId() {
        return $this['id'];
    }
    
    public function getEmail() {
        return $this['email'];
    }
    
    public function getFullName() {
        return $this['fullName'];
    }
    
    public function getNickName() {
        return $this['nickName'];
    }
    
    public function getStatus() {
        return $this['status'];
    }
    
    public function getJoinDate() {
        return $this['joinDate'];
    }
    
    public function getLoginDate() {
        return $this['loginDate'];
    }
    
    public function getLanguage() {
        return $this['language'];
    }
    
    public function getCountry() {
        return $this['country'];
    }
    
    public function getTimezone() {
        return $this['timezone'];
    }
    
    
    public function onAuthentication() {
        $this->loginDate = 'now';
        $this->save();
    }
}
