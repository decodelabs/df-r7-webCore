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

class IndexHeaderBar extends arch\component\HeaderBar
{
    protected $icon = 'menu';

    protected function getDefaultTitle()
    {
        return $this->_('System menus');
    }

    protected function addOperativeLinks(MenuWidget $menu): void
    {
        $menu->addLinks(
            $this->html->link(
                    $this->uri('./refresh', true),
                    $this->_('Refresh menu list')
                )
                ->setIcon('refresh')
        );
    }
}
