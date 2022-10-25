<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\mail\capture\_nodes;

use df\arch;

class HttpDeleteAll extends arch\node\DeleteForm
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const ITEM_NAME = 'mailbox';

    protected function init(): void
    {
        $this->data->checkAccess('axis://mail/Capture', 'delete');
    }

    protected function apply()
    {
        $this->data->getModel('mail')->capture->delete()->execute();
    }
}
