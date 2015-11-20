<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\theme\layouts\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;
use df\fire;

class HttpDeleteSlot extends arch\node\DeleteForm {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'slot';

    protected $_layout;
    protected $_slot;

    protected function init() {
        $config = fire\Config::getInstance();

        if(!$this->_layout = $config->getLayoutDefinition($this->request['layout'])) {
            $this->throwError(404, 'Layout not found');
        }

        if(!$this->_slot = $this->_layout->getSlot($this->request['slot'])) {
            $this->throwError(404, 'Slot not found');
        }
    }

    protected function getInstanceId() {
        return $this->_layout->getId().':'.$this->_slot->getId();
    }

    protected function createItemUi($container) {
        $container->push(
            $this->html->attributeList($this->_slot)
                // Id
                ->addField('id', function($slot) {
                    return $slot->getId();
                })

                // Name
                ->addField('name', function($slot) {
                    return $slot->getName();
                })
        );
    }

    protected function apply() {
        $config = fire\Config::getInstance();
        $this->_layout->removeSlot($this->_slot);

        $config->setLayoutDefinition($this->_layout)->save();
    }
}