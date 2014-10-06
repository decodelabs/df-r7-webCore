<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\schedule\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\halo;
    
class HttpNudge extends arch\form\template\Confirm {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'spool';

    protected function _getMainMessage($itemName) {
        return $this->_('Are you sure you want to launch the spool daemon?');
    }

    protected function _apply() {
        $remote = halo\daemon\Remote::factory('TaskSpool');
        $remote->start();

        sleep(2);
    }
}