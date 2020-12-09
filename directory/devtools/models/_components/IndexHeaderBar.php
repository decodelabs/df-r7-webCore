<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_components;

use df;
use df\core;
use df\apex;
use df\arch;

use df\aura\html\widget\Menu as MenuWidget;

class IndexHeaderBar extends arch\component\HeaderBar
{
    protected $icon = 'database';

    protected function getDefaultTitle()
    {
        return $this->_('Data models');
    }

    protected function addSubOperativeLinks(MenuWidget $menu): void
    {
        switch ($this->request->getNode()) {
            case 'index':
                $menu->addLinks(
                    $this->html->link(
                            $this->uri('~devtools/models/update', true),
                            $this->_('Update schemas')
                        )
                        ->setIcon('update')
                        ->setDisposition('operative')
                );

                break;

            case 'backups':
                $menu->addLinks(
                    $this->html->link(
                            $this->uri('~devtools/models/backup', true),
                            $this->_('Create backup')
                        )
                        ->setIcon('backup')
                        ->setDisposition('positive')
                );

                break;
        }
    }

    protected function addSectionLinks(MenuWidget $menu): void
    {
        $menu->addLinks(
            $this->html->link(
                    '~devtools/models/',
                    $this->_('Units')
                )
                ->setIcon('unit')
                ->setDisposition('informative'),

            $this->html->link(
                    '~devtools/models/backups',
                    $this->_('Backups')
                )
                ->setIcon('backup')
                ->setDisposition('informative')
        );
    }
}
