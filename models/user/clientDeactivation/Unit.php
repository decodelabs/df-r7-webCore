<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\user\clientDeactivation;

use df;
use df\core;
use df\apex;
use df\axis;
use df\opal;
    
class Unit extends axis\unit\table\Base {

    protected function _onCreate(axis\schema\ISchema $schema) {
        $schema->addPrimaryField('id', 'AutoId');
        $schema->addField('user', 'One', 'client');
        $schema->addField('date', 'Timestamp');
        $schema->addField('reason', 'String', 255)
            ->isNullable(true);
        $schema->addField('comments', 'BigString', 'medium')
            ->isNullable(true);
    }

    public function applyPagination(opal\query\IPaginator $paginator) {
        $paginator
            ->setOrderableFields('date', 'user')
            ->setDefaultOrder('date DESC');

        return $this;
    }
}