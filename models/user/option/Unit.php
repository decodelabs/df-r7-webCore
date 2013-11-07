<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\user\option;

use df;
use df\core;
use df\axis;
use df\opal;

class Unit extends axis\unit\table\Base {
    
    protected function _onCreate(axis\schema\ISchema $schema) {
        $schema->addField('user', 'ManyToOne', 'client', 'options');
        $schema->addField('key', 'String', 255);
        $schema->addField('data', 'String', 1024);

        $schema->addPrimaryIndex('primary', ['user', 'key']);
    }

    public function fetchOption($userId, $key, $default=null) {
        $output = $this->select('data')
            ->where('user', '=', $userId)
            ->where('key', '=', $key)
            ->toValue('data');

        if($output === null) {
            $output = $default;
        }

        return $output;
    }

    public function setOption($userId, $key, $value) {
        $this->replace([
                'user' => $userId,
                'key' => $key,
                'data' => $value
            ])
            ->execute();

        return $this;
    }
}
