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
    
class HttpScan extends arch\form\template\Confirm {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'scan';

    protected function _getMainMessage($itemName) {
        return $this->_('Are you sure you want to scan for new tasks now?');
    }

    protected function _renderItemDetails($container) {
        $container->push(
            $this->html->checkbox('reset', $this->values->reset, $this->_(
                'Reset all auto tasks back to original state'
            ))
        );
    }

    protected function _apply() {
        $validator = $this->data->newValidator()
            ->addField('reset', 'boolean')
                ->end()
            ->validate($this->values);


        $task = 'manager/scan';

        if($validator['reset']) {
            $task .= '?reset';
        }

        return $this->task->initiateStream($task);
    }
}