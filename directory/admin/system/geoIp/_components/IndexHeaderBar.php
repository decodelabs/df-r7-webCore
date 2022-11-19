<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\geoIp\_components;

use df\arch;

use df\aura\html\widget\Menu as MenuWidget;

class IndexHeaderBar extends arch\component\HeaderBar
{
    protected $icon = 'location';

    protected function getDefaultTitle()
    {
        return $this->_('Geo IP settings');
    }

    /*
    protected function addOperativeLinks(MenuWidget $menu): void
    {
        $menu->addLinks(
            $this->html->link(
                    $this->uri('./delete-all', true),
                    $this->_('Delete all errors')
                )
                ->setIcon('delete')
        );
    }
    */

    protected function addSectionLinks(MenuWidget $menu): void
    {
        $menu->addLinks(
            $this->html->link('./', $this->_('Details'))
                ->setIcon('details')
                ->setDisposition('informative'),
            $this->html->link('./max-mind-db', $this->_('MaxMind DB'))
                ->setIcon('database')
                ->setDisposition('informative')
        );
    }
}
