<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\mail\lists\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\flow;

class HttpRefresh extends arch\node\ConfirmForm
{
    public const DISPOSITION = 'positive';

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to refresh the mailing list cache?');
    }

    protected function customizeMainButton($button)
    {
        $button->setBody($this->_('Refresh'))
            ->setIcon('refresh');
    }

    protected function apply()
    {
        flow\Manager::getInstance()->refreshListCache();
    }
}
