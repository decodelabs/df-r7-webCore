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
    
class DetailHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'mail';

    protected function _getDefaultTitle() {
        return $this->_('Mail: %s%', ['%s%' => $this->_record['subject']]);
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request(
                        '~devtools/mail/dev/delete?mail='.$this->_record['id'], true,
                        '~devtools/mail/dev/'
                    ),
                    $this->_('Delete email')
                )
                ->setIcon('delete')
                ->addAccessLock($this->_record->getActionLock('delete'))
        );
    }
}