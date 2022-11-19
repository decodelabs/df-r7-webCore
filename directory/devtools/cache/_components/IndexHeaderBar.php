<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\cache\_components;

use df\arch;

use df\aura\html\widget\Menu as MenuWidget;

class IndexHeaderBar extends arch\component\HeaderBar
{
    protected $icon = 'toolkit';

    protected function getDefaultTitle()
    {
        return $this->_('Cache control');
    }

    protected function addOperativeLinks(MenuWidget $menu): void
    {
        $menu->addLinks(
            $this->html->link(
                $this->uri('~devtools/cache/purge', true),
                $this->_('Purge all cache backends')
            )
                ->setIcon('delete')
                ->setDisposition('negative')
        );
    }
}
