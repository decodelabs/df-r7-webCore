<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\schedule\_nodes;

use df\arch;

class HttpScan extends arch\node\ConfirmForm
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const ITEM_NAME = 'scan';

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to scan for new tasks now?');
    }

    protected function createItemUi($container)
    {
        $container->push(
            $this->html->checkbox('reset', $this->values->reset, $this->_(
                'Reset all auto tasks back to original state'
            ))
        );
    }

    protected function apply()
    {
        $validator = $this->data->newValidator()
            ->addRequiredField('reset', 'boolean')
            ->validate($this->values);


        $task = 'tasks/scan';

        if ($validator['reset']) {
            $task .= '?reset';
        }

        return $this->task->initiateStream($task);
    }
}
