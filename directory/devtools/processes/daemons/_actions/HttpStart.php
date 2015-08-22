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
    
class HttpStart extends arch\form\template\Confirm {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'daemon';

    protected $_daemon;

    protected function init() {
        $this->_daemon = halo\daemon\Base::factory($this->request->query['daemon']);
    }

    protected function getMainMessage() {
        return $this->_(
            'Are you sure you want to start the %n% daemon?',
            ['%n%' => $this->_daemon->getName()]
        );
    }

    protected function apply() {
        $task = 'daemons/remote?daemon='.$this->_daemon->getName().'&command=start';
        return $this->task->initiateStream($task);
    }
}