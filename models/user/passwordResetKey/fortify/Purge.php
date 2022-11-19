<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\user\passwordResetKey\fortify;

use df\axis;

class Purge extends axis\fortify\Base
{
    protected function execute()
    {
        $total = 0;

        while (true) {
            $items = $this->_unit->select('id')

                ->orWhereCorrelation('user', '!in', 'id')
                    ->from('axis://user/Client')
                    ->endCorrelation()
                ->orWhere('user', '=', null)

                ->orWhere('creationDate', '<', '-1 months')

                ->limit(100)
                ->toArray();

            if (empty($items)) {
                break;
            }

            foreach ($items as $item) {
                $total += $this->_unit->delete()
                    ->where('id', '=', $item['id'])
                    ->execute();
            }

            usleep(100000);
        }

        yield $total . ' removed';
    }
}
