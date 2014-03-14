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
    
class UnitDetailHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'unit';

    protected function _getDefaultTitle() {
        return $this->_('Unit: %t%', [
            '%t%' => $this->_record->getId()
        ]);
    }

    protected function _addOperativeLinks($menu) {
        switch($this->_record->getType()) {
            case 'table':
                $menu->addLinks(
                    $this->html->link(
                            $this->uri->request('~devtools/models/rebuild-table?unit='.$this->_record->getId(), true),
                            $this->_('Rebuild table')
                        )
                        ->setIcon('refresh')
                        ->setDisposition('operative')
                );

                break;
        }
    }

    protected function _addSubOperativeLinks($menu) {
        switch($this->_record->getType()) {
            case 'table':
                switch($this->request->getAction()) {
                    case 'backups':
                        $menu->addLinks(
                            $this->html->link(
                                    $this->uri->request('~devtools/models/backup-table?unit='.$this->_record->getId(), true),
                                    $this->_('Make backup')
                                )
                                ->setIcon('backup')
                                ->setDisposition('positive')
                        );

                        break;
                }

                break;
        }
    }

    protected function _addSectionLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    '~devtools/models/unit-details?unit='.$this->_record->getId(),
                    $this->_('Details')
                )
                ->setIcon('details')
        );

        switch($this->_record->getType()) {
            case 'table':
                $menu->addLinks(
                    $this->html->link(
                            '~devtools/models/table-data?unit='.$this->_record->getId(),
                            $this->_('Data')
                        )
                        ->setIcon('list'),

                    $this->html->link(
                            '~devtools/models/backups?unit='.$this->_record->getId(),
                            $this->_('Backups')
                        )
                        ->setIcon('backup')
                );

                break;
        }
    }
}