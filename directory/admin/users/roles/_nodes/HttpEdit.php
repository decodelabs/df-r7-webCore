<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\roles\_nodes;

class HttpEdit extends HttpAdd
{
    protected function init()
    {
        $this->_role = $this->scaffold->getRecord();
    }

    protected function getInstanceId()
    {
        return $this->_role['id'];
    }

    protected function setDefaultValues(): void
    {
        $this->values->importFrom($this->_role, [
            'name', 'signifier', 'priority'
        ]);
    }
}
