<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\schedule\_nodes;

use df\arch;
use df\halo;

class HttpNudge extends arch\node\ConfirmForm
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const ITEM_NAME = 'spool';

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to launch the spool daemon?');
    }

    protected function apply()
    {
        $remote = halo\daemon\Remote::factory('TaskSpool');
        $remote->start();

        sleep(2);
    }
}
