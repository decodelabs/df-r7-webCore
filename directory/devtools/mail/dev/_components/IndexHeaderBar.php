<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\mail\dev\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class IndexHeaderBar extends arch\component\template\HeaderBar {

    protected function _getDefaultTitle() {
        return $this->_('Dev mail');
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~devtools/mail/dev/delete-all', true),
                    $this->_('Delete all mail')
                )
                ->setIcon('delete')
                ->addAccessLock('axis://mail/DevMail#delete')
        );
    }
}