<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\media\files\_nodes;

use df\arch\node\form\SelectorDelegate;

class HttpEdit extends HttpAdd
{
    protected function init(): void
    {
        $this->_file = $this->scaffold->getRecord();
    }

    protected function getInstanceId(): ?string
    {
        return $this->_file['id'];
    }

    protected function setDefaultValues(): void
    {
        $this->values->importFrom($this->_file, [
            'fileName'
        ]);

        /** @var SelectorDelegate $bucket */
        $bucket = $this['bucket'];
        $bucket->setSelected($this->_file['#bucket']);

        /** @var SelectorDelegate $owner */
        $owner = $this['owner'];
        $owner->setSelected($this->_file['#owner']);

        $this->values->overwriteName = true;
    }
}
