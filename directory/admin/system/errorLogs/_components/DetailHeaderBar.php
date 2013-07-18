<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\errorLogs\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class DetailHeaderBar extends arch\component\template\HeaderBar {

    protected function _getDefaultTitle() {
        return $this->_('Error log: %m% %c% - %d%', [
            '%m%' => $this->_record['mode'], 
            '%c%' => $this->_record['code'],
            '%d%' => $this->format->userDateTime($this->_record['date'])
        ]);
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            // Delete
            $this->import->component('LogLink', '~admin/system/error-logs/', $this->_record, $this->_('Delete log'))
                ->setAction('delete')
                ->setRedirectTo('~admin/system/error-logs/')
        );
    }
}