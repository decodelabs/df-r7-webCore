<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\groups\_nodes;

use df\arch\node\form\SelectorDelegate;

class HttpEdit extends HttpAdd
{
    protected function init(): void
    {
        $this->_group = $this->scaffold->getRecord();
    }

    protected function getInstanceId(): ?string
    {
        return $this->_group['id'];
    }

    protected function setDefaultValues(): void
    {
        $this->values->importFrom($this->_group, ['name', 'signifier']);

        /** @var SelectorDelegate $roles */
        $roles = $this['roles'];
        $roles->setSelected($this->_group['#roles']);
    }
}
