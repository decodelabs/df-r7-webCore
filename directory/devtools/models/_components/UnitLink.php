<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\models\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class UnitLink extends arch\component\template\RecordLink {

    protected $_icon = 'unit';
    protected $_maxLength = 35;

// Url
    protected function _getRecordUrl($id) {
        return '~devtools/models/unit-details?unit='.$id;
    }

    protected function _getRecordId() {
        return $this->_record->getId();
    }

    protected function _getRecordName() {
        return $this->_record->getId();
    }
}