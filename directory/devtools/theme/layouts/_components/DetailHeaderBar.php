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

class DetailHeaderBar extends arch\component\HeaderBar {

    protected $_icon = 'layout';

    protected function _getDefaultTitle() {
        return $this->_('Layout: %n%', ['%n%' => $this->_record->getId()]);
    }

    protected function _addOperativeLinks($menu) {
        $layoutId = $this->_record->getId();

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

    protected function _addSubOperativeLinks($menu) {
        if($this->request->isAction('slots')) {
            $layoutId = $this->_record->getId();

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

    protected function _addSectionLinks($menu) {
        $layoutId = $this->_record->getId();
        $slotCount = $this->_record->countSlots();

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
                ->setNote($this->format->counterNote($slotCount))
                ->setIcon('list')
                ->setDisposition('informative')
        );
    }
}