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
            throw core\Error::{'fire/layout/ENotFound'}([
                'message' => 'Layout not found',
                'http' => 404
            ]);
        }

        if(!$this->_slot = $this->_layout->getSlot($this->request['slot'])) {
            throw core\Error::{'fire/slot/ENotFound'}([
                'message' => 'Slot not found',
                'http' => 404
            ]);
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
        $this->_layout->removeSlot($this->_slot->getId());

        $config->setLayoutDefinition($this->_layout)->save();
    }
}