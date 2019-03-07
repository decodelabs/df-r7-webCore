<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\user\passwordResetKey\fortify;

use df;
use df\core;
use df\apex;
use df\axis;

class Purge extends axis\fortify\Base
{
    protected function execute()
    {
        $total = 0;

        while (true) {
            $total += $count = $this->_unit->delete()
                ->orWhereCorrelation('user', '!in', 'id')
                    ->from('axis://user/Client')
                    ->endCorrelation()
                ->orWhere('user', '=', null)
                ->orWhere('creationDate', '<', '-1 months')
                ->limit(100)
                ->execute();

            if (!$count) {
                break;
            }

            usleep(100000);
        }

        yield $total.' removed';
    }
}
