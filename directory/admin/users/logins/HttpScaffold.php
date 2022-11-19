<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\logins;

use df\arch;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const TITLE = 'Login attempts';
    public const ICON = 'form';
    public const ADAPTER = 'axis://user/Login';

    public const NAME_FIELD = 'date';

    public const LIST_FIELDS = [
        'date', 'identity', 'user', 'ip',
        'authenticated', 'actions' => false
    ];

    public const DETAILS_FIELDS = [
        'date', 'identity', 'user', 'ip',
        'agent', 'authenticated'
    ];

    public const CAN_ADD = false;
    public const CAN_EDIT = false;
    public const CAN_DELETE = false;

    public const SEARCH_FIELDS = [
        'user.email' => 4,
        'user.fullName' => 6,
        'identity' => 10,
        'ip' => 14
    ];


    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query
            ->importRelationBlock('user', 'link');
    }


    // Nodes
    public function failedHtmlNode()
    {
        return $this->buildRecordListNode(function ($query) {
            $query->where('authenticated', '=', false);
        });
    }



    // Components
    public function generateIndexSectionLinks(): iterable
    {
        yield 'all' => $this->html->link($this->getNodeUri('index'), 'All', true)
            ->setIcon('star')
            ->setDisposition('informative');

        yield 'failed' => $this->html->link($this->getNodeUri('failed'), 'Failed', true)
            ->setIcon('deny')
            ->setDisposition('informative');
    }


    // Fields
    public function defineUserField($list, $mode)
    {
        $list->addField('user', function ($login) {
            return $this->apex->component('../clients/UserLink', $login['user'])
                ->isNullable(true);
        });
    }

    public function defineAuthenticatedField($list, $mode)
    {
        $list->addField('authenticated', 'Success', function ($login) {
            return $this->html->yesNoIcon($login['authenticated']);
        });
    }
}
