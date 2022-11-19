<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\system\pestControl\reports\_nodes;

use df\arch;

class HttpPurgeAll extends arch\node\ConfirmForm
{
    public const DISPOSITION = 'negative';

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to delete ALL HTTP reports?');
    }

    protected function customizeMainButton($button)
    {
        $button->setBody($this->_('Delete'))
            ->setIcon('delete');
    }

    protected function apply()
    {
        return $this->task->initiateStream('pest-control/purge-reports?all');
    }
}
