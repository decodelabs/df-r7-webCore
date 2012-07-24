<?php

namespace df\apex\models\user\auth;

use df\core;
use df\axis;
use df\opal;
use df\user;

class Record extends opal\query\record\Base implements user\authentication\IDomainInfo {
    
    public function getIdentity() {
        return $this['identity'];
    }
    
    public function getPassword() {
        return $this['password'];
    }
    
    public function getBindDate() {
        return $this['bindDate'];
    }
    
    public function getClientData() {
        return $this['user'];
    }
    
    public function onAuthentication() {
        $this->loginDate = 'now';
        $this->save();
        
        return $this;
    }
}
