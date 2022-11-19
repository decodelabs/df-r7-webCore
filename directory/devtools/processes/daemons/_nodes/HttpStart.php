<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\processes\daemons\_nodes;

use df\arch;
use df\halo;

class HttpStart extends arch\node\ConfirmForm
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const ITEM_NAME = 'daemon';

    protected $_daemon;

    protected function init(): void
    {
        $this->_daemon = halo\daemon\Base::factory($this->request['daemon']);
    }

    protected function getMainMessage()
    {
        return $this->_(
            'Are you sure you want to start the %n% daemon?',
            ['%n%' => $this->_daemon->getName()]
        );
    }

    protected function apply()
    {
        return $this->task->initiateStream(
            'daemons/remote?daemon=' . $this->_daemon->getName() . '&command=start'
        );
    }
}
