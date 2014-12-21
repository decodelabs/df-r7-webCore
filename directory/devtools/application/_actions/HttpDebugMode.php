<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\application\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\halo;
    
class HttpDebugMode extends arch\form\template\Confirm {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'application';

    protected $_isEnabled;

    protected function _init() {
        $this->_isEnabled = $this->http->request->hasCookie('debug');
    }

    protected function _getMainMessage($itemName) {
        if($this->_isEnabled) {
            return $this->_('Are you sure you want to turn off debug mode?');
        } else {
            return $this->_('Are you sure you want to enable debugging for this session?');
        }
    }

    protected function _apply() {
        $augmentor = $this->application->getResponseAugmentor();
        $cookie = $augmentor->newCookie('debug', '1', null, true);

        if($this->_isEnabled) {
            $augmentor->removeCookieForAnyRequest($cookie);
            $this->comms->removeQueuedFlash('global.debug');
        } else {
            $augmentor->setCookieForAnyRequest($cookie);
        }
    }
}