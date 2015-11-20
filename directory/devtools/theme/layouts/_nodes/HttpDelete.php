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

class HttpDelete extends arch\node\DeleteForm {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const ITEM_NAME = 'layout';

    protected $_layout;

    protected function init() {
        $config = fire\Config::getInstance();

        if(!$this->_layout = $config->getLayoutDefinition($this->request['layout'])) {
            $this->throwError(404, 'Layout not found');
        }
    }

    protected function getInstanceId() {
        return $this->_layout->getId();
    }

    protected function createItemUi($container) {
        $container->push(
            $this->html->attributeList($this->_layout)
                // Id
                ->addField('id', function($layout) {
                    return $layout->getId();
                })

                // Name
                ->addField('name', function($layout) {
                    return $layout->getName();
                })

                // Slots
                ->addField('slots', function($layout) {
                    return $layout->countSlots();
                })
        );
    }

    protected function apply() {
        $config = fire\Config::getInstance();
        $config->removeLayoutDefinition($this->_layout)->save();
    }
}