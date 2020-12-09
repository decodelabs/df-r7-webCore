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

use df\aura\html\widget\Menu as MenuWidget;

class DetailHeaderBar extends arch\component\HeaderBar
{
    protected $icon = 'menu';

    protected function getDefaultTitle()
    {
        return $this->_('Menu: %n%', ['%n%' => $this->record->getDisplayName()]);
    }

    protected function addOperativeLinks(MenuWidget $menu): void
    {
        $menuId = $this->record->getId()->path->toString();

        $menu->addLinks(
            $this->html->link(
                    $this->uri('./edit?menu='.$menuId, true),
                    $this->_('Edit menu')
                )
                ->setIcon('edit')
        );
    }
}
