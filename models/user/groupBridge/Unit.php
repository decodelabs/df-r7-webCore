<?php

namespace df\apex\models\user\groupBridge;

use df\core;
use df\axis;

class Unit extends axis\unit\table\ManyBridge {
    
    protected $_dominantUnitName = 'client';
    protected $_dominantFieldName = 'groups';
    
    protected function _onCreate(axis\schema\ISchema $schema) {
        $schema->addField('isLeader', 'Boolean');
    }
}
