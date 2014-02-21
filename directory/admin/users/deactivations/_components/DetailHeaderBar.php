<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\deactivations\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class DetailHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'remove';

    protected function _getDefaultTitle() {
        return $this->_('User deactivation: %n%', [
            '%n%' => $this->_record['user']['fullName']
        ]);
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            // Delete
            $this->import->component('DeactivationLink', '~admin/users/deactivations/', $this->_record, $this->_('Delete info'))
                ->setAction('delete')
                ->setRedirectTo('~admin/users/deactivations/')
        );
    }
}