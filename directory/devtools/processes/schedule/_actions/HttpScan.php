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

    protected function getMainMessage() {
        return $this->_('Are you sure you want to scan for new tasks now?');
    }

    protected function createItemUi($container) {
        $container->push(
            $this->html->checkbox('reset', $this->values->reset, $this->_(
                'Reset all auto tasks back to original state'
            ))
        );
    }

    protected function apply() {
        $validator = $this->data->newValidator()
            ->addField('reset', 'boolean')
            ->validate($this->values);


        $task = 'tasks/scan';

        if($validator['reset']) {
            $task .= '?reset';
        }

        return $this->task->initiateStream($task);
    }
}