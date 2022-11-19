<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\logs\_nodes;

use df\arch;

class HttpDeleteAll extends arch\node\DeleteForm
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const ITEM_NAME = 'log list';

    protected function createItemUi($container)
    {
        $container->addAttributeList([])
            ->addField('logs', function () {
                return $this->data->task->log->countAll();
            });
    }

    protected function apply()
    {
        $this->data->task->log->delete()->execute();
    }
}
