<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\axis;

class HttpClearCache extends arch\form\template\Confirm {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const DISPOSITION = 'negative';

    protected $_inspector;

    protected function init() {
        $probe = new axis\introspector\Probe();

        if(!$this->_inspector = $probe->inspectUnit($this->request->query['unit'])) {
            $this->throwError(404, 'Unit not found');
        }

        if($this->_inspector->getType() != 'cache') {
            $this->throwError(401, 'Unit not a cache');
        }
    }

    protected function getInstanceId() {
        return $this->_inspector->getId();
    }

    protected function getMainMessage() {
        return $this->_('Are you sure you want to clear this cache?');
    }

    protected function createItemUi($container) {
        $container->addAttributeList($this->_inspector)
            ->addField('unit', function($inspector) {
                return $inspector->getId();
            })
            ->addField('backend', function($inspector) {
                return $inspector->getAdapterName();
            })
            ->addField('entries', function($inspector) {
                return $inspector->getUnit()->count();
            });
    }

    protected function customizeMainButton($button) {
        $button->setBody($this->_('Clear'))
            ->setIcon('delete');
    }

    protected function apply() {
        $this->_inspector->getUnit()->clear();
    }
}