<?php

namespace df\apex\models\user\group;

use df\core;
use df\axis;

class Unit extends axis\unit\table\Base {
    
    protected function _onCreate(axis\schema\ISchema $schema) {
        $schema->addField('id', 'AutoId', 4);
        $schema->addField('name', 'String', 64);
        $schema->addField('users', 'ManyToMany', 'client', 'groups');
        $schema->addField('roles', 'ManyToMany', 'role', 'groups')->isDominant(true);
        $schema->addField('meta', 'DataObject');
        
        $schema->addPrimaryIndex('id');
    }
}
