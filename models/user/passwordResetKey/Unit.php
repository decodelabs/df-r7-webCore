<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\user\passwordResetKey;

use df;
use df\core;
use df\apex;
use df\axis;
    
class Unit extends axis\unit\table\Base {

    protected function createSchema($schema) {
        $schema->addField('id', 'AutoId');

        $schema->addField('user', 'One', 'user/client');

        $schema->addUniqueField('key', 'String', 40);

        $schema->addField('adapter', 'String', 32)
            ->setDefaultValue('Local');

        $schema->addField('creationDate', 'Timestamp');

        $schema->addField('resetDate', 'DateTime')
            ->isNullable(true);
    }

    public function pruneUnusedKeys() {
        $this->delete()
            ->where('creationDate', '<', '-1 week')
            ->where('resetDate', '=', null)
            ->execute();

        return $this;
    }

    public function deleteRecentUnusedKeys(Record $redeemedKey) {
        $this->delete()
            ->where('user', '=', $redeemedKey['#user'])
            ->where('resetDate', '=', null)
            ->where('adapter', '=', 'Local')
            ->execute();

        return $this;
    }
}