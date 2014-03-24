<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\accessErrors\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class DetailHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'lock';

    protected function _getDefaultTitle() {
        return $this->_('Access error log: %m% - %d%', [
            '%m%' => $this->_record['mode'], 
            '%d%' => $this->format->userDateTime($this->_record['date'])
        ]);
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            // Delete
            $this->import->component('ErrorLink', '~admin/system/access-errors/', $this->_record, $this->_('Delete error'))
                ->setAction('delete')
                ->setRedirectTo('~admin/system/access-errors/')
        );
    }
}