<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\shared\media\_formDelegates;

class ImageSelector extends FileSelector
{
    protected function init(): void
    {
        $this->setAcceptTypes('image/*');
        parent::init();
    }
}
