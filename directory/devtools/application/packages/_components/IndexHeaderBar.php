<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\application\packages\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class IndexHeaderBar extends arch\component\template\HeaderBar {

    protected function _getDefaultTitle() {
        return $this->_('Packages');
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~devtools/application/packages/add', true),
                    $this->_('Install new package')
                )
                ->setIcon('add')
                ->isDisabled(true),

            $this->html->link(
                    $this->uri->request('~devtools/application/packages/refresh-all', true),
                    $this->_('Refresh')
                )
                ->setIcon('refresh'),

            $this->html->link(
                    $this->uri->request('~devtools/application/packages/update-all', true),
                    $this->_('Update all')
                )
                ->setIcon('download')
                ->setDisposition('operative'),

            $this->html->link(
                    $this->uri->request('~devtools/application/packages/commit-all', true),
                    $this->_('Commit all')
                )
                ->setIcon('upload')
                ->setDisposition('operative')
        );
    }
}