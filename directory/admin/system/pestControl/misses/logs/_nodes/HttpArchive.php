<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\system\pestControl\misses\logs\_nodes;

use df\arch;

class HttpArchive extends arch\node\ConfirmForm
{
    public const ITEM_NAME = 'log';

    protected $_log;

    protected function init(): void
    {
        $this->_log = $this->scaffold->getRecord();
    }

    protected function getInstanceId(): ?string
    {
        return $this->_log['id'];
    }

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to archive this log?');
    }

    protected function createItemUi($container)
    {
        $container->push(
            $this->apex->component('LogDetails')
                ->setRecord($this->_log)
        );
    }

    protected function customizeMainButton($button)
    {
        $button->setBody($this->_('Archive'))
            ->setIcon('save');
    }

    protected function apply()
    {
        $this->_log->isArchived = true;
        $this->_log->save();
    }
}
