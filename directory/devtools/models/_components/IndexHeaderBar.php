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
    
class IndexHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'database';

    protected function _getDefaultTitle() {
        return $this->_('Data models');
    }

    protected function _addSubOperativeLinks($menu) {
        switch($this->request->getAction()) {
            case 'index':
                $menu->addLinks(
                    $this->html->link(
                            $this->uri->request('~devtools/models/update', true),
                            $this->_('Update schemas')
                        )
                        ->setIcon('update')
                        ->setDisposition('operative')
                );

                break;

            case 'backups':
                $menu->addLinks(
                    $this->html->link(
                            $this->uri->request('~devtools/models/backup', true),
                            $this->_('Create backup')
                        )
                        ->setIcon('backup')
                        ->setDisposition('positive')
                );

                break;
        }
    }

    protected function _addSectionLinks($menu) {
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