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

class Unit extends axis\unit\table\Base {

    protected $_defaultOrderableFields = [
        'date', 'user'
    ];

    protected $_defaultOrder = 'date DESC';

    protected function createSchema($schema) {
        $schema->addPrimaryField('id', 'AutoId');
        $schema->addField('user', 'One', 'client');
        $schema->addField('date', 'Timestamp');
        $schema->addField('reason', 'Text', 255)
            ->isNullable(true);
        $schema->addField('comments', 'BigText', 'medium')
            ->isNullable(true);
    }
}