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
use df\flex;

class Record extends opal\record\Base {

    protected function _onPreInsert() {
        if(!$this['key']) {
            $this['key'] = flex\Generator::sessionId();
        }
    }

    public function sendAsAllowance($rendererPath=null) {
        return $this->getAdapter()->sendAsAllowance($this, $rendererPath);
    }

    public function forceSendAsAllowance($rendererPath=null) {
        return $this->getAdapter()->forceSendAsAllowance($this, $rendererPath);
    }

    public function send($rendererPath=null) {
        return $this->getAdapter()->send($this, $rendererPath);
    }

    public function forceSend($rendererPath=null) {
        return $this->getAdapter()->forceSend($this, $rendererPath);
    }

    public function resend($rendererPath=null) {
        return $this->getAdapter()->resend($this, $rendererPath);
    }

    public function forceResend($rendererPath=null) {
        return $this->getAdapter()->forceResend($this, $rendererPath);
    }

    public function claim(user\IClientDataObject $client) {
        return $this->getAdapter()->claim($this, $client);
    }
}
