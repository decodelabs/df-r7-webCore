<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\error\log;

use df;
use df\core;
use df\apex;
use df\axis;
use df\opal;
    
class Unit extends axis\unit\table\Base {

    protected function _onCreate(axis\schema\ISchema $schema) {
        $schema->addField('id', 'AutoId', 8);

        $schema->addIndexedField('code', 'Integer', 2);
        
        $schema->addIndexedField('date', 'Timestamp');

        $schema->addField('mode', 'String', 16);

        $schema->addField('request', 'String', 255);

        $schema->addField('exceptionType', 'String', 255)
            ->isNullable(true);

        $schema->addField('message', 'BigString', 'medium');

        $schema->addField('user', 'One', 'user/client')
            ->isNullable(true);

        $schema->addField('isProduction', 'Boolean')
            ->setDefaultValue(true);
    }

    public function applyPagination(opal\query\IPaginator $paginator) {
        $paginator
            ->setOrderableFields('code', 'date', 'mode', 'request', 'user')
            ->setDefaultOrder('date DESC');

        return $this;
    }
}