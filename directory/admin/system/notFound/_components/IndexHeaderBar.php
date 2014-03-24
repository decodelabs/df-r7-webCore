<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\notFound\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class IndexHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'brokenLink';

    protected function _getDefaultTitle() {
        return $this->_('404 error logs');
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~admin/system/not-found/delete-all', true),
                    $this->_('Delete all errors')
                )
                ->setIcon('delete')
        );
    }
}