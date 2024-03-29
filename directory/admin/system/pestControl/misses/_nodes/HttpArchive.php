<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\system\pestControl\misses\_nodes;

use df\arch;

class HttpArchive extends arch\node\ConfirmForm
{
    public const ITEM_NAME = 'error';
    public const DISPOSITION = 'negative';

    protected $_miss;

    protected function init(): void
    {
        $this->_miss = $this->scaffold->getRecord();
    }

    protected function getInstanceId(): ?string
    {
        return $this->_miss['id'];
    }

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to archive this error?');
    }

    protected function createItemUi($container)
    {
        $container->push(
            $this->apex->component('MissDetails')
                ->setRecord($this->_miss)
        );
    }

    protected function customizeMainButton($button)
    {
        $button->setBody($this->_('Archive'))
            ->setIcon('remove');
    }

    protected function apply()
    {
        $this->_miss->archiveDate = 'now';
        $this->_miss->save();
    }
}
