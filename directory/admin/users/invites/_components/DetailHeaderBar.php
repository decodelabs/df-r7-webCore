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
    
class DetailHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'mail';

    protected function _getDefaultTitle() {
        return $this->_('Invite: %e%', ['%e%' => $this->_record['email']]);
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            // Resend
            $this->import->component('InviteLink', '~admin/users/invites/', $this->_record, $this->_('Resend invite'))
                ->setAction('resend')
                ->setIcon('refresh')
                ->setDisposition('positive')
                ->isDisabled(!$this->_record['isActive'] || $this->_record['registrationDate']),

            // Deactivate
            $this->import->component('InviteLink', '~admin/users/invites/', $this->_record, $this->_('Deactivate invite'))
                ->setAction('deactivate')
                ->setIcon('remove')
                ->setDisposition('negative')
                ->isDisabled(!$this->_record['isActive'])
        );
    }
}