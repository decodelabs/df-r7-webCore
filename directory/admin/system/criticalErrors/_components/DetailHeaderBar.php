<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\criticalErrors\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class DetailHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'bug';

    protected function _getDefaultTitle() {
        return $this->_('Critical error log: %m% - %d%', [
            '%m%' => $this->_record['mode'], 
            '%d%' => $this->format->userDateTime($this->_record['date'])
        ]);
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            // Delete
            $this->import->component('ErrorLink', '~admin/system/critical-errors/', $this->_record, $this->_('Delete error'))
                ->setAction('delete')
                ->setRedirectTo('~admin/system/critical-errors/')
        );
    }
}