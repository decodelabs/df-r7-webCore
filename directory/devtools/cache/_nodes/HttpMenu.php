<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\cache\_nodes;

use df\arch;

class HttpMenu extends arch\node\Form
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const DEFAULT_EVENT = 'refresh';

    protected $_cache;

    protected function init(): void
    {
        $this->_cache = arch\navigation\menu\Cache::getInstance();
    }

    protected function createUi(): void
    {
        $form = $this->content->addForm();
        $menus = arch\navigation\menu\Base::loadList($this->context, $this->_cache->getKeys());

        $form->push(
            $this->html->collectionList($menus)
                ->setErrorMessage($this->_('There are currently no cached menu manifests'))

                // Id
                ->addField('id', function ($menu) {
                    return (string)$menu->getId()->getPath();
                })

                // Source
                ->addField('source', function ($menu) {
                    return $menu->getId()->getScheme();
                })

                // Delegates
                ->addField('delegates', function ($menu) {
                    return count($menu->getDelegates());
                })

                // Entries
                ->addField('entries', function ($menu) {
                    return $menu->generateEntries()->count();
                })

                // Actions
                ->addField('actions', function ($menu) {
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

    protected function onRemoveEvent(string $id): mixed
    {
        if ($this->_cache->has($id)) {
            $this->_cache->remove($id);

            $this->comms->flashSuccess(
                'cache.remove',
                $this->_('The menu cache %n% has been successfully been removed', ['%n%' => $id])
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
            $this->_cache->clear();

            $this->comms->flashSuccess(
                'cache.clear',
                $this->_('All menu caches have been cleared')
            );
        });
    }
}
