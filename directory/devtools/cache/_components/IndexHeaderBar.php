<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\cache\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class IndexHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'toolkit';

    protected function _getDefaultTitle() {
        return $this->_('Cache control');
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~devtools/cache/purge', true),
                    $this->_('Purge all cache backends')
                )
                ->setIcon('delete')
                ->setDisposition('negative')
        );
    }
}