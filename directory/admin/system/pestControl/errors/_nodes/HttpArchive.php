<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\system\pestControl\errors\_nodes;

use df\arch;

class HttpArchive extends arch\node\ConfirmForm
{
    public const ITEM_NAME = 'error';
    public const DISPOSITION = 'negative';

    protected $_error;

    protected function init(): void
    {
        $this->_error = $this->scaffold->getRecord();
    }

    protected function getInstanceId(): ?string
    {
        return $this->_error['id'];
    }

    protected function getMainMessage()
    {
        return $this->_('Are you sure you want to archive this error?');
    }

    protected function createItemUi($container)
    {
        $container->push(
            $this->apex->component('ErrorDetails')
                ->setRecord($this->_error)
        );
    }

    protected function customizeMainButton($button)
    {
        $button->setBody($this->_('Archive'))
            ->setIcon('remove');
    }

    protected function apply()
    {
        $this->_error->archiveDate = 'now';
        $this->_error->save();
    }
}
