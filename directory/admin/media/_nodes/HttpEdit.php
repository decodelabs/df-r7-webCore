<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\media\_nodes;

class HttpEdit extends HttpAdd
{
    protected function init(): void
    {
        $this->_bucket = $this->scaffold->getRecord();
    }

    protected function getInstanceId(): ?string
    {
        return $this->_bucket['id'];
    }

    protected function setDefaultValues(): void
    {
        $this->values->importFrom($this->_bucket, [
            'name', 'slug'
        ]);
    }
}
