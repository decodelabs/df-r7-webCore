<?php

namespace df\apex\models\user\client;

use df\core;
use df\axis;

class Unit extends axis\unit\table\Base {
    
    protected function _onCreate(axis\schema\ISchema $schema) {
        $schema->addField('id', 'AutoId', 8);
        $schema->addField('email', 'String', 128);
        $schema->addField('fullName', 'String', 255);
        $schema->addField('nickName', 'String', 128)->isNullable(true);
        $schema->addField('joinDate', 'Date');
        $schema->addField('loginDate', 'DateTime')->isNullable(true);
        
        $schema->addField('groups', 'ManyToMany', 'group', 'users')
            ->isDominant(true)
            ->setBridgeUnitId('groupBridge')
            ;
            
        $schema->addField('authDomains', 'OneToMany', 'auth', 'user');
        $schema->addField('status', 'Integer', 1)->setDefaultValue(3);
        
        $schema->addField('timezone', 'String', 32)->setDefaultValue('UTC');
        $schema->addField('country', 'KeyString', 2, core\string\ICase::UPPER)->setDefaultValue('GB');
        $schema->addField('language', 'KeyString', 2, core\string\ICase::LOWER)->setDefaultValue('en');
        
        $schema->addPrimaryIndex('id');
        $schema->addUniqueIndex('email');
        $schema->addIndex('loginDate');
        $schema->addIndex('status');
    }
}
