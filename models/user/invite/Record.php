<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\user\invite;

use df;
use df\core;
use df\axis;
use df\opal;

class Record extends opal\record\Base {
    
    protected function _onPreInsert() {
        if(!$this['key']) {
            $this['key'] = core\string\Generator::sessionId();
        }
    }

    public function sendAsAllowance($templatePath=null, $templateLocation=null) {
        return $this->getRecordAdapter()->sendAsAllowance($this, $templatePath, $templateLocation);
    }

    public function send($templatePath=null, $templateLocation=null) {
        return $this->getRecordAdapter()->send($this, $templatePath, $templateLocation);
    }

    public function resend($templatePath=null, $templateLocation=null) {
        return $this->getRecordAdapter()->resend($this, $templatePath, $templateLocation);
    }

    public function claim(user\IClientDataObject $client) {
        return $this->getRecordAdapter()->claim($this, $client);
    }
}
