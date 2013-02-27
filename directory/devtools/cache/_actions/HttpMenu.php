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
    
class HttpMenu extends arch\form\Action {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const DEFAULT_EVENT = 'refresh';

    protected $_cache;

    protected function _init() {
        $this->_cache = arch\navigation\menu\Cache::getInstance($this->application);
    }

    protected function _createUi() {
        $form = $this->content->addForm();
        $menus = arch\navigation\menu\Base::loadList($this->context, $this->_cache->getKeys());

        $form->push(
            $this->html->collectionList($menus)
                ->setErrorMessage($this->_('There are currently no cached menu manifests'))

                // Id
                ->addField('id', function($menu) {
                    return (string)$menu->getId()->getPath();
                })

                // Source
                ->addField('source', function($menu) {
                    return $menu->getId()->getScheme();
                })

                // Delegates
                ->addField('delegates', function($menu) {
                    return count($menu->getDelegates());
                })

                // Entries
                ->addField('entries', function($menu) {
                    return $menu->generateEntries()->count();
                })

                // Actions
                ->addField('actions', function($menu) {
                    return $this->html->eventButton(
                            $this->eventName('remove', (string)$menu->getId()),
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

    protected function _onRemoveEvent($id) {
        if($this->_cache->has($id)) {
            $this->_cache->remove($id);

            $this->comms->notify(
                    'cache.remove',
                    $this->_('The menu cache %n% has been successfully been removed', ['%n%' => $id]),
                    'success'
                )
                ->setDescription($this->_(
                    'The entry will not show up again here until the cache has been regenerated'
                ));
        }
    }

    protected function _onClearEvent() {
        $this->_cache->clear();

        $this->comms->notify(
            'cache.clear',
            $this->_('All menu caches have been cleared'),
            'success'
        );

        return $this->complete();
    }

    protected function _onRefreshEvent() {}
}