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

use df\aura\html\widget\Menu as MenuWidget;

class UnitDetailHeaderBar extends arch\component\HeaderBar
{
    protected $icon = 'unit';
    protected $_storageExists = false;

    protected function getDefaultTitle()
    {
        return $this->_('Unit: %t%', [
            '%t%' => $this->record->getId()
        ]);
    }

    public function setRecord($record)
    {
        if ($record) {
            $this->_storageExists = $record->storageExists();
        } else {
            $this->_storageExists = false;
        }

        return parent::setRecord($record);
    }

    protected function addOperativeLinks(MenuWidget $menu): void
    {
        switch ($this->record->getType()) {
            case 'cache':
                $menu->addLinks(
                    $this->html->link(
                            $this->uri('~devtools/models/clear-cache?unit='.$this->record->getId(), true),
                            $this->_('Clear cache')
                        )
                        ->setIcon('delete')
                );

                break;

            case 'table':
                $menu->addLinks(
                    $this->html->link(
                            $this->uri('~devtools/models/rebuild-table?unit='.$this->record->getId(), true),
                            $this->_('Rebuild table')
                        )
                        ->setIcon('refresh')
                        ->setDisposition('operative')
                        //->isDisabled(!$this->_storageExists)
                );

                break;
        }
    }

    protected function addSubOperativeLinks(MenuWidget $menu): void
    {
        switch ($this->request->getNode()) {
            case 'tableBackups':
                $menu->addLinks(
                    $this->html->link(
                            $this->uri('~devtools/models/purge-table-backups?unit='.$this->record->getId(), true),
                            $this->_('Delete all backups')
                        )
                        ->setIcon('delete')
                );

                break;
        }
    }

    protected function addSectionLinks(MenuWidget $menu): void
    {
        $menu->addLinks(
            $this->html->link(
                    '~devtools/models/unit-details?unit='.$this->record->getId(),
                    $this->_('Details')
                )
                ->setIcon('details')
        );

        switch ($this->record->getType()) {
            case 'cache':
                $menu->addLinks(
                    $this->html->link(
                            '~devtools/models/cache-stats?unit='.$this->record->getId(),
                            $this->_('Stats')
                        )
                        ->setIcon('report')
                );

                break;

            case 'table':
                $menu->addLinks(
                    $this->html->link(
                            '~devtools/models/table-data?unit='.$this->record->getId(),
                            $this->_('Data')
                        )
                        ->setIcon('list')
                        ->isDisabled(!$this->_storageExists),

                    $this->html->link(
                            '~devtools/models/table-backups?unit='.$this->record->getId(),
                            $this->_('Backups')
                        )
                        ->setIcon('backup')
                );

                break;
        }
    }
}
