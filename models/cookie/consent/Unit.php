<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\cookie\consent;

use df;
use df\core;
use df\apex;
use df\axis;

class Unit extends axis\unit\Table
{
    const ORDERABLE_FIELDS = [
        'creationDate', 'preferences',
        'statistics', 'marketing'
    ];

    const DEFAULT_ORDER = 'creationDate DESC';

    protected function createSchema($schema)
    {
        $schema->addPrimaryField('id', 'Guid');

        $schema->addField('creationDate', 'Timestamp');
        $schema->addField('preferences', 'Boolean');
        $schema->addField('statistics', 'Boolean');
        $schema->addField('marketing', 'Boolean');
    }
}
