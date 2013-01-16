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

class HttpAxis extends arch\form\Action {

    const DEFAULT_ACCESS = arch\IAccess::DEV;

    protected $_cache;

    protected function _init() {
        $this->_cache = axis\schema\Cache::getInstance($this->application);
    }

    protected function _createUi() {
        $form = $this->content->addForm();
        $keys = $this->_cache->getKeys();
        $info = axis\Unit::getUnitMetadata($keys);

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
                })
        );

        $form->push(
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

    protected function _onRemoveEvent($unitId) {
        if($this->_cache->has($unitId)) {
            $this->_cache->remove($unitId);

            $this->arch->notify(
                    'cache.remove',
                    $this->_('The schema cache %n% has been successfully been removed', ['%n%' => $unitId]),
                    'success'
                )
                ->setDescription($this->_(
                    'The entry will not show up again here until the cache has been regenerated'
                ));
        }
    }

    protected function _onClearEvent() {
        $this->_cache->clear();

        $this->arch->notify(
            'cache.clear',
            $this->_('All schema caches have been cleared'),
            'success'
        );

        return $this->complete();
    }

    protected function _onRefreshEvent() {}
}