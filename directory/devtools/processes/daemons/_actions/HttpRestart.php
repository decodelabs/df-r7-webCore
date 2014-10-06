<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\daemons\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\halo;
    
class HttpRestart extends arch\form\template\Confirm {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'daemon';

    protected $_daemon;

    protected function _init() {
        $this->_daemon = halo\daemon\Base::factory($this->request->query['daemon']);
    }

    protected function _getMainMessage($itemName) {
        return $this->_(
            'Are you sure you want to restart the %n% daemon?',
            ['%n%' => $this->_daemon->getName()]
        );
    }

    protected function _apply() {
        $task = 'daemons/remote?daemon='.$this->_daemon->getName().'&command=restart';
        return $this->task->initiateStream($task);
    }
}