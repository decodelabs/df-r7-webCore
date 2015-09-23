<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\shared\media\_formDelegates;

use df;
use df\core;
use df\apex;
use df\arch;
use df\aura;

class ImageSelector extends FileSelector {

    protected function init() {
        $this->setAcceptTypes('image/*');
        return parent::init();
    }
}