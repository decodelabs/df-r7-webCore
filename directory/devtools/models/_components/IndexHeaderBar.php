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

/*
    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~admin/siteData/industries/add', true),
                    $this->_('Add new industry')
                )
                ->setIcon('add')
                ->addAccessLock('axis://wecommend/Industry#add')
        );
    }

    protected function _addTransitiveLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    '~admin/userData/companies/',
                    $this->_('Companies')
                )
                ->setIcon('company')
                ->setDisposition('transitive')
        );
    }
*/
}