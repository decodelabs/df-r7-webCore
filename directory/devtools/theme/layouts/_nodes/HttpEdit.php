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

class HttpEdit extends HttpAdd
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
    }

    protected function getInstanceId()
    {
        return $this->_layout->getId();
    }

    protected function setDefaultValues()
    {
        $this->values->id = $this->_layout->getId();
        $this->values->name = $this->_layout->getName();
        $this->values->areas = implode(', ', $this->_layout->getAreas());
    }
}
