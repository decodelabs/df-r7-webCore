<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\deactivations;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

use DecodeLabs\Metamorph;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const TITLE = 'User deactivations';
    public const ICON = 'remove';
    public const ADAPTER = 'axis://user/ClientDeactivation';
    public const KEY_NAME = 'deactivation';
    public const NAME_FIELD = 'date';

    public const CAN_ADD = false;
    public const CAN_EDIT = false;

    public const LIST_FIELDS = [
        'date', 'user', 'reason'
    ];

    public const DETAILS_FIELDS = [
        'user', 'date', 'reason', 'comments'
    ];

    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query->importRelationBlock('user', 'link');
    }

    protected function searchRecordList($query, $search)
    {
        $query->searchFor($search, [
            'user|fullName' => 5
        ]);
    }

    // Components
    public function generateIndexTransitiveLinks(): iterable
    {
        yield 'clients' => $this->html->link('../clients/', $this->_('All users'))
            ->setIcon('user')
            ->setDisposition('transitive');
    }


    // Fields
    public function defineCommentsField($list)
    {
        $list->addField('comments', function ($deactivation) {
            return Metamorph::text($deactivation['comments']);
        });
    }
}
