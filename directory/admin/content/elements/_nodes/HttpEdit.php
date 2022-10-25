<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\content\elements\_nodes;

use df\apex\directory\shared\nightfire\_formDelegates\ContentSlot;

class HttpEdit extends HttpAdd
{
    protected function init(): void
    {
        $this->_element = $this->scaffold->getRecord();
    }

    protected function getInstanceId(): ?string
    {
        return $this->_element['id'];
    }

    protected function setDefaultValues(): void
    {
        $this->values->importFrom($this->_element, [
            'slug', 'name'
        ]);

        /** @var ContentSlot */
        $body = $this['body'];
        $body->setSlotContent($this->_element['body']);
    }
}
