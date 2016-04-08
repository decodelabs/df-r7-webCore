<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\mail\journal;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

class HttpScaffold extends arch\scaffold\RecordAdmin {

    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const TITLE = 'Send logs';
    const ICON = 'log';
    const ADAPTER = 'axis://mail/Journal';
    const NAME_FIELD = 'date';
    const CAN_ADD = false;
    const CAN_EDIT = false;
    const CAN_DELETE = false;

    const LIST_FIELDS = [
        'date', 'name', 'email', 'user', 'environmentMode', 'expireDate', 'actions' => false
    ];

    const DETAILS_FIELDS = [
        'date', 'name', 'email', 'user', 'environmentMode', 'expireDate',
        'key1', 'key2'
    ];


// Record data
    protected function prepareRecordList($query, $mode) {
        $query->importRelationBlock('user', 'link');
    }

    protected function searchRecordList($query, $search) {
        $query->searchFor($search, [
            'name' => 3,
            'email' => 1,
            'user|fullName' => 3
        ]);
    }


// Fields
    public function defineNameField($list, $mode) {
        $list->addField('name', $this->_('Template'), function($log) {
            $name = $log['name'];

            if(0 === strpos($name, '~mail/')) {
                $name = substr($name, 6);
            }

            if(substr($name, 0, 1) != '~') {
                return $this->html->link('~mail/templates/view?path='.$name, $name)
                    ->setIcon('theme')
                    ->setDisposition('transitive');
            }

            return $name;
        });
    }

    public function defineUserField($list, $mode) {
        $list->addField('user', function($log) {
            return $this->apex->component('~admin/users/clients/UserLink', $log['user'])
                ->isNullable(true);
        });
    }

    public function defineExpireDateField($list, $mode) {
        $list->addField('expireDate', $this->_('Expires'), function($log) {
            return $this->html->timeFromNow($log['expireDate']);
        });
    }
}