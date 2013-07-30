<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\cache\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\neon;
    
class HttpRaster extends arch\form\Action {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const DEFAULT_EVENT = 'refresh';

    protected $_cache;

    protected function _init() {
        $this->_cache = neon\raster\Cache::getInstance($this->application);
    }
    
    protected function _createUi() {
        $form = $this->content->addForm();
        $files = $this->_cache->getDirectFileList();

        $form->push(
            $this->html->collectionList($files)
                ->setErrorMessage($this->_('There are currently no images cached'))

                // Key
                ->addField('file id', function($file, $context) {
                    $parts = explode('-', $context->getKey());

                    $context->setStore('transformHash', array_pop($parts));
                    $context->setStore('fileId', $id = implode('-', $parts));


                    return $id;
                })

                // Transform
                ->addField('transformationId', function($file, $context) {
                    return $context->getStore('transformHash');
                })

                // Size
                ->addField('size', function($file) {
                    return $this->format->fileSize($file->getSize());
                })

                // Created
                ->addField('created', function($file) {
                    return $this->html->userDateTime($file->getLastModified());
                })

                // Actions
                ->addField('actions', function($file, $context) {
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

    protected function _onRemoveEvent($key) {
        if($this->_cache->has($key)) {
            $this->_cache->remove($key);

            $this->comms->flash(
                    'cache.remove',
                    $this->_('The image cache has been successfully been removed'),
                    'success'
                )
                ->setDescription($this->_(
                    'The entry will not show up again here until the cache has been regenerated'
                ));
        }
    }

    protected function _onClearEvent() {
        $this->_cache->clear();

        $this->comms->flash(
            'cache.clear',
            $this->_('All image caches have been cleared'),
            'success'
        );

        return $this->complete();
    }

    protected function _onRefreshEvent() {}
}