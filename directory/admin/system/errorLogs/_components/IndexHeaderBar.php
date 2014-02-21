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
    
class IndexHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'log';

    protected function _getDefaultTitle() {
        return $this->_('Error logs');
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~admin/system/error-logs/delete-all', true),
                    $this->_('Delete all logs')
                )
                ->setIcon('delete')
        );
    }
}