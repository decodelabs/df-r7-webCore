<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\journal;

use DecodeLabs\Tagged as Html;

use df\arch;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const TITLE = 'Send logs';
    public const ICON = 'log';
    public const ADAPTER = 'axis://mail/Journal';
    public const NAME_FIELD = 'date';
    public const CAN_ADD = false;
    public const CAN_EDIT = false;
    public const CAN_DELETE = false;

    public const LIST_FIELDS = [
        'date', 'name', 'email', 'user', 'environmentMode', 'expireDate', 'actions' => false
    ];

    public const DETAILS_FIELDS = [
        'date', 'name', 'email', 'user', 'environmentMode', 'expireDate',
        'key1', 'key2'
    ];


    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query->importRelationBlock('user', 'link');
    }

    protected function searchRecordList($query, $search)
    {
        $query->searchFor($search, [
            'name' => 3,
            'email' => 1,
            'user|fullName' => 3
        ]);
    }


    // Fields
    public function defineNameField($list, $mode)
    {
        $list->addField('name', $this->_('Template'), function ($log) {
            $name = $log['name'];

            if (0 === strpos($name, '~mail/')) {
                $name = substr($name, 6);
            }

            if (substr($name, 0, 1) != '~') {
                return $this->html->link('~mail/previews/view?path=' . $name, $name)
                    ->setIcon('theme')
                    ->setDisposition('transitive');
            }

            return $name;
        });
    }

    public function defineUserField($list, $mode)
    {
        $list->addField('user', function ($log) {
            return $this->apex->component('~admin/users/clients/UserLink', $log['user'])
                ->isNullable(true);
        });
    }

    public function defineExpireDateField($list, $mode)
    {
        $list->addField('expireDate', $this->_('Expires'), function ($log) {
            return Html::$time->until($log['expireDate']);
        });
    }
}
