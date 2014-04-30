<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\user\emailVerify;

use df;
use df\core;
use df\apex;
use df\axis;
    
class Unit extends axis\unit\table\Base {

    protected function _onCreate(axis\schema\ISchema $schema) {
        $schema->addField('user', 'One', 'client');
        $schema->addField('email', 'String', 255);
        $schema->addField('key', 'String', 16);
        $schema->addField('creationDate', 'Timestamp');
        $schema->addField('verifyDate', 'DateTime')
            ->isNullable(true);

        $schema->addPrimaryIndex('primary', ['user', 'email']);
    }

    public function fetchEmailList(apex\models\user\client\Record $client) {
        $output = $this->select()
            ->where('user', '=', $client['id'])
            ->orderBy('creationDate DESC')
            ->toKeyArray('email');

        if(!isset($output[$client['email']])) {
            $output[$client['email']] = [
                'user' => $client['id'],
                'email' => $client['email'],
                'key' => null,
                'creationDate' => new core\time\Date(),
                'verifyDate' => null
            ];
        }

        return $output;
    }

    public function isVerified($userId, $email) {
        return (bool)$this->select()
            ->where('user', '=', $userId)
            ->where('email', '=', $email)
            ->where('verifyDate', '!=', null)
            ->count();
    }

    public function verify($userId, $key) {
        $record = $this->fetch()
            ->where('user', '=', $userId)
            ->where('key', '=', $key)
            ->toRow();

        if(!$record) {
            return false;
        }

        $record['verifyDate'] = 'now';
        $record->save();

        return true;
    }
}