<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\cookie\consent\fortify;

use df;
use df\core;
use df\apex;
use df\axis;

class Purge extends axis\fortify\Base
{
    protected function execute()
    {
        $count = $this->_unit->delete()
            ->beginWhereClause()
                ->where('preferences', '<', '-1 year')
                ->orWhere('preferences', '=', null)
                ->endClause()
            ->beginWhereClause()
                ->where('statistics', '<', '-1 year')
                ->orWhere('statistics', '=', null)
                ->endClause()
            ->beginWhereClause()
                ->where('marketing', '<', '-1 year')
                ->orWhere('marketing', '=', null)
                ->endClause()
            ->execute();

        yield $count.' removed';
    }
}
