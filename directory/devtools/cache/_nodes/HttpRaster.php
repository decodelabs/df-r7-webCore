<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\cache\_nodes;

use DecodeLabs\Tagged as Html;
use df\arch;

use df\neon;

class HttpRaster extends arch\node\Form
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const DEFAULT_EVENT = 'refresh';

    protected $_fileStore;

    protected function init(): void
    {
        $this->_fileStore = neon\raster\FileStore::getInstance();
    }

    protected function createUi(): void
    {
        $form = $this->content->addForm();
        $files = $this->_fileStore->getFileList();

        $form->push(
            $this->html->collectionList($files)
                ->setErrorMessage($this->_('There are currently no images cached'))

                // Key
                ->addField('file id', function ($file, $context) {
                    $parts = explode('-', $context->getKey());

                    $context->setStore('transformHash', array_pop($parts));
                    $context->setStore('fileId', $id = implode('-', $parts));


                    return $id;
                })

                // Transform
                ->addField('transformationId', function ($file, $context) {
                    return $context->getStore('transformHash');
                })

                // Size
                ->addField('size', function ($file) {
                    return Html::$number->fileSize($file->getSize());
                })

                // Created
                ->addField('created', function ($file) {
                    return Html::$time->dateTime($file->getLastModified());
                })

                // Actions
                ->addField('actions', function ($file, $context) {
                    return $this->html->eventButton(
                        $this->eventName('remove', $context->getKey()),
                        $this->_('Clear')
                    )
                        ->setIcon('delete');
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

    protected function onRemoveEvent(string $key): mixed
    {
        if ($this->_fileStore->has($key)) {
            $this->_fileStore->remove($key);

            $this->comms->flashSuccess(
                'cache.remove',
                $this->_('The image cache has been successfully been removed')
            )
                ->setDescription($this->_(
                    'The entry will not show up again here until the cache has been regenerated'
                ));
        }

        return null;
    }

    protected function onClearEvent(): mixed
    {
        return $this->complete(function () {
            $this->_fileStore->clear();

            $this->comms->flashSuccess(
                'cache.clear',
                $this->_('All image caches have been cleared')
            );
        });
    }
}
