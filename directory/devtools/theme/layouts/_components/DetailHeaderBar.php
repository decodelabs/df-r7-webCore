<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\theme\layouts\_components;

use df;
use df\core;
use df\apex;
use df\arch;

use df\aura\html\widget\Menu as MenuWidget;

use DecodeLabs\Dictum;

class DetailHeaderBar extends arch\component\HeaderBar
{
    protected $icon = 'layout';

    protected function getDefaultTitle()
    {
        return $this->_('Layout: %n%', ['%n%' => $this->record->getId()]);
    }

    protected function addOperativeLinks(MenuWidget $menu): void
    {
        $layoutId = $this->record->getId();

        $menu->addLinks(
            $this->html->link(
                    $this->uri('~devtools/theme/layouts/edit?layout='.$layoutId, true),
                    $this->_('Edit layout')
                )
                ->setIcon('edit'),

            $this->html->link(
                    $this->uri(
                        '~devtools/theme/layouts/delete?layout='.$layoutId, true,
                        '~devtools/theme/layouts/'
                    ),
                    $this->_('Delete layout')
                )
                ->setIcon('delete')
        );
    }

    protected function addSubOperativeLinks(MenuWidget $menu): void
    {
        if ($this->request->isNode('slots')) {
            $layoutId = $this->record->getId();

            $menu->addLinks(
                $this->html->link(
                        $this->uri('~devtools/theme/layouts/add-slot?layout='.$layoutId, true),
                        $this->_('Add slot')
                    )
                    ->setIcon('add'),

                $this->html->link(
                        $this->uri('~devtools/theme/layouts/reorder-slots?layout='.$layoutId, true),
                        $this->_('Re-order')
                    )
                    ->setIcon('refresh')
                    ->setDisposition('operative')
            );
        }
    }

    protected function addSectionLinks(MenuWidget $menu): void
    {
        $layoutId = $this->record->getId();
        $slotCount = $this->record->countSlots();

        $menu->addLinks(
            $this->html->link(
                    '~devtools/theme/layouts/details?layout='.$layoutId,
                    $this->_('Details'),
                    true
                )
                ->setIcon('details')
                ->setDisposition('informative'),

            $this->html->link(
                    '~devtools/theme/layouts/slots?layout='.$layoutId,
                    $this->_('Slots'),
                    true
                )
                ->setNote(Dictum::$number->counter($slotCount))
                ->setIcon('list')
                ->setDisposition('informative')
        );
    }
}
