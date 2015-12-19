<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\user\passwordResetKey;

use df;
use df\core;
use df\axis;
use df\opal;
use df\flex;

class Record extends opal\record\Base {

    public function generateKey() {
        $unit = $this->getAdapter();

        do {
            $key = flex\Generator::sessionId();
            $count = $unit->select()
                ->where('key', '=', $key)
                ->where('id', '!=', $this['id'])
                ->count();
        } while($count);

        $this->key = $key;
        return $this;
    }

    public function hasExpired() {
        return core\time\Date::factory('-2 days')->gt($this['creationDate']);
    }

    public function isRedeemed() {
        return $this['resetDate'] !== null;
    }
}
