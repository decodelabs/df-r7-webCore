<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\user\passwordResetKey;

use df\axis;

class Unit extends axis\unit\Table
{
    protected function createSchema($schema)
    {
        $schema->addField('id', 'AutoId');

        $schema->addField('user', 'One', 'user/client');

        $schema->addUniqueField('key', 'Text', 40);

        $schema->addField('adapter', 'Text', 32)
            ->setDefaultValue('Local');

        $schema->addField('creationDate', 'Timestamp');

        $schema->addField('resetDate', 'Date:Time')
            ->isNullable(true);
    }

    public function pruneUnusedKeys()
    {
        $this->delete()
            ->where('creationDate', '<', '-1 week')
            ->where('resetDate', '=', null)
            ->execute();

        return $this;
    }

    public function deleteRecentUnusedKeys(Record $redeemedKey)
    {
        $this->delete()
            ->where('user', '=', $redeemedKey['#user'])
            ->where('resetDate', '=', null)
            ->where('adapter', '=', 'Local')
            ->execute();

        return $this;
    }
}
