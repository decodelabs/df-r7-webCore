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

use DecodeLabs\Exceptional;

class HttpEditSlot extends HttpAddSlot
{
    protected function init()
    {
        $config = fire\Config::getInstance();

        if (!$this->_layout = $config->getLayoutDefinition($this->request['layout'])) {
            throw Exceptional::{'df/fire/layout/NotFound'}([
                'message' => 'Layout not found',
                'http' => 404
            ]);
        }

        if (!$this->_slot = $this->_layout->getSlot($this->request['slot'])) {
            throw Exceptional::{'df/fire/slot/NotFound'}([
                'message' => 'Slot not found',
                'http' => 404
            ]);
        }
    }

    protected function setDefaultValues()
    {
        $this->values->id = $this->_slot->getId();
        $this->values->name = $this->_slot->getName();
        $this->values->minBlocks = $this->_slot->getMinBlocks();
        $this->values->maxBlocks = $this->_slot->getMaxBlocks();
        $this->values->category = $this->_slot->getCategory();
    }
}
