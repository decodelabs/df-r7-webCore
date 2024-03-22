<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\cache\_nodes;

use DecodeLabs\Stash\Store;
use df\arch;

class HttpAxis extends arch\node\Form
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const DEFAULT_EVENT = 'refresh';

    protected Store $_cache;

    protected function init(): void
    {
        $this->_cache = $this->data->axis->getSchemaManager()->getCache();
    }

    protected function createUi(): void
    {
        $form = $this->content->addForm();

        $form->push(
            $this->html->attributeList($this->_cache)
                ->addField('name', function ($cache) {
                    return 'Axis schema cache';
                }),
            $this->html->eventButton(
                'clear',
                $this->_('Clear all')
            )
                ->setIcon('delete'),
            $this->html->eventButton(
                'refresh',
                $this->_('Refresh')
            )
                ->setIcon('refresh'),
            $this->html->eventButton(
                'cancel',
                $this->_('Done')
            )
                ->setIcon('back')
                ->setDisposition('positive')
        );
    }

    protected function onClearEvent(): mixed
    {
        return $this->complete(function () {
            $this->_cache->clear();

            $this->comms->flashSuccess(
                'cache.clear',
                $this->_('All schema caches have been cleared')
            );
        });
    }
}
