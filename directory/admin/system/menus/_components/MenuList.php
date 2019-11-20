<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\menus\_components;

use df;
use df\core;
use df\apex;
use df\arch;

use DecodeLabs\Tagged\Html;

class MenuList extends arch\component\CollectionList
{
    protected $_fields = [
        'name' => true,
        'packages' => true,
        'actions' => true
    ];


    // Name
    public function addNameField($list)
    {
        $list->addField('name', function ($menu) {
            $idString = $menu->getId()->path->toString();
            $parts = explode('/', $idString);
            array_pop($parts);
            $path = implode('/', $parts);

            return $this->html->link(
                    $this->uri('./details?menu='.$idString, true),
                    [
                        Html::{'span.inactive'}($path.'/'),
                        $menu->getDisplayName()
                    ]
                )
                ->setIcon('menu')
                ->setDisposition('informative');
        });
    }


    // Packages
    public function addPackagesField($list)
    {
        $list->addField('packages', function ($menu) {
            foreach ($menu->getDelegates() as $delegate) {
                $idString = $delegate->getId()->path->toString();

                if (!$subId = $delegate->getSubId()) {
                    continue;
                }

                yield $this->html->link(
                        $this->uri('./details?menu='.$idString, true),
                        $this->format->name($subId)
                    )
                    ->setIcon('plugin');
            }
        });
    }


    // Actions
    public function addActionsField($list)
    {
        $list->addField('actions', function ($menu) {
            return [
                $this->html->link(
                        $this->uri('./edit?menu='.$menu->getId()->path->toString(), true),
                        $this->_('Edit')
                    )
                    ->setIcon('edit')
            ];
        });
    }
}
