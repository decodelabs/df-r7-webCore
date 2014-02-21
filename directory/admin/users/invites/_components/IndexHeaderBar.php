<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\invites\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class IndexHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'mail';

    protected function _getDefaultTitle() {
        return $this->_('Invites');
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~admin/users/invites/send', true),
                    $this->_('Invite new user')
                )
                ->setIcon('add')
                ->addAccessLock('axis://user/Invite#add')
        );
    }

    protected function _addSubOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~admin/users/invites/grant', true),
                    $this->_('Grant allowance')
                )
                ->setIcon('edit'),

            $this->html->link(
                    $this->uri->request('~admin/users/settings', true),
                    $this->_('Settings')
                )
                ->setIcon('settings')
                ->setDisposition('operative')
        );
    }
}