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
use df\axis;

class HttpAxis extends arch\action\Form {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const DEFAULT_EVENT = 'refresh';

    protected $_cache;

    protected function init() {
        $this->_cache = axis\schema\Cache::getInstance();
    }

    protected function createUi() {
        $form = $this->content->addForm();
        $keys = $this->_cache->getKeys();
        $info = axis\Model::getUnitMetaData($keys);

        $form->push(
            $this->html->collectionList($info)
                ->setErrorMessage($this->_('There are currently no schemas cached'))

                // Model
                ->addField('model')

                // Name
                ->addField('name')

                // Storage name
                ->addField('canonicalName')

                // Type
                ->addField('type')

                // Actions
                ->addField('actions', function($info) {
                    return $this->html->eventButton(
                            $this->eventName('remove', $info['unitId']),
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

    protected function onRemoveEvent($unitId) {
        if($this->_cache->has($unitId)) {
            $this->_cache->remove($unitId);

            $this->comms->flashSuccess(
                    'cache.remove',
                    $this->_('The schema cache %n% has been successfully been removed', ['%n%' => $unitId])
                )
                ->setDescription($this->_(
                    'The entry will not show up again here until the cache has been regenerated'
                ));
        }
    }

    protected function onClearEvent() {
        return $this->complete(function() {
            $this->_cache->clear();

            $this->comms->flashSuccess(
                'cache.clear',
                $this->_('All schema caches have been cleared')
            );
        });
    }
}