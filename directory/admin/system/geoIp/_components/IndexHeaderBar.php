<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\geoIp\_components;

use df;
use df\core;
use df\apex;
use df\arch;

class IndexHeaderBar extends arch\component\HeaderBar {

    protected $_icon = 'location';

    protected function _getDefaultTitle() {
        return $this->_('Geo IP settings');
    }

/*
    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri('./delete-all', true),
                    $this->_('Delete all errors')
                )
                ->setIcon('delete')
        );
    }
*/

    protected function _addSectionLinks($menu) {
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