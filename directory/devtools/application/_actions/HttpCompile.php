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
    
class HttpCompile extends arch\form\template\Confirm {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'application';

    protected function _getMainMessage($itemName) {
        return $this->_('Are you sure you want to re-compile the production version of this application?');
    }

    protected function _apply() {
        $view = $this->aura->getView('CompileResult.html');
        $view['result'] = halo\process\Base::launchTask('application/build');

        return $view;
    }
}