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
use df\axis;

class UnitDetailHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'unit';
    protected $_storageExists = false;

    protected function _getDefaultTitle() {
        return $this->_('Unit: %t%', [
            '%t%' => $this->_record->getId()
        ]);
    }

    public function setRecord($record) {
        if($record) {
            $this->_storageExists = $record->storageExists();
        } else {
            $this->_storageExists = false;
        }

        return parent::setRecord($record);
    }

    protected function _addOperativeLinks($menu) {
        switch($this->_record->getType()) {
            case 'cache':
                $menu->addLinks(
                    $this->html->link(
                            $this->uri('~devtools/models/clear-cache?unit='.$this->_record->getId(), true),
                            $this->_('Clear cache')
                        )
                        ->setIcon('delete')
                );

                break;

            case 'table':
                $menu->addLinks(
                    $this->html->link(
                            $this->uri('~devtools/models/rebuild-table?unit='.$this->_record->getId(), true),
                            $this->_('Rebuild table')
                        )
                        ->setIcon('refresh')
                        ->setDisposition('operative')
                        //->isDisabled(!$this->_storageExists)
                );

                break;
        }
    }

    protected function _addSubOperativeLinks($menu) {
        switch($this->request->getAction()) {
            case 'tableBackups':
                $menu->addLinks(
                    $this->html->link(
                            $this->uri('~devtools/models/backup-table?unit='.$this->_record->getId(), true),
                            $this->_('Make backup')
                        )
                        ->setIcon('backup')
                        ->setDisposition('positive')
                        ->isDisabled(!$this->_storageExists),

                    $this->html->link(
                            $this->uri('~devtools/models/purge-table-backups?unit='.$this->_record->getId(), true),
                            $this->_('Delete all backups')
                        )
                        ->setIcon('delete')
                );

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
            case 'cache':
                $menu->addLinks(
                    $this->html->link(
                            '~devtools/models/cache-stats?unit='.$this->_record->getId(),
                            $this->_('Stats')
                        )
                        ->setIcon('report')
                );

                break;

            case 'table':
                $menu->addLinks(
                    $this->html->link(
                            '~devtools/models/table-data?unit='.$this->_record->getId(),
                            $this->_('Data')
                        )
                        ->setIcon('list')
                        ->isDisabled(!$this->_storageExists),

                    $this->html->link(
                            '~devtools/models/table-backups?unit='.$this->_record->getId(),
                            $this->_('Backups')
                        )
                        ->setIcon('backup')
                );

                break;
        }
    }
}