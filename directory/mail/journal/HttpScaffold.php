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

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const DIRECTORY_TITLE = 'Send logs';
    const DIRECTORY_ICON = 'log';
    const RECORD_ADAPTER = 'axis://mail/Journal';
    const RECORD_NAME_KEY = 'date';
    const CAN_ADD_RECORD = false;
    const CAN_EDIT_RECORD = false;
    const CAN_DELETE_RECORD = false;

    protected $_recordListFields = [
        'date', 'name', 'email', 'user'
    ];


// Record data
    protected function _prepareRecordListQuery(opal\query\ISelectQuery $query, $mode) {
        $query->importRelationBlock('user', 'link');
    }

    public function applyRecordQuerySearch(opal\query\ISelectQuery $query, $search, $mode) {
        $query->beginWhereClause()
            ->orWhere('name', 'matches', $search)
            ->orWhere('email', 'matches', $search)
            ->orWhereCorrelation('user', 'in', 'id')
                ->from('axis://user/Client', 'client')
                ->where('client.fullName', 'matches', $search)
                ->orWhere('client.nickName', 'matches', $search)
                ->endCorrelation()
            ->endClause();
    }


// Fields
    public function defineNameField($list, $mode) {
        $list->addField('name', $this->_('Template'), function($log) {
            $name = $log['name'];

            if(0 === strpos($name, 'mail/')) {
                $name = substr($name, 5);

                return $this->html->link('~mail/templates/view?path='.$name, $name)
                    ->setIcon('theme')
                    ->setDisposition('transitive');
            }

            return $name;
        });
    }

    public function defineUserField($list, $mode) {
        $list->addField('user', function($log) {
            return $this->import->component('~admin/users/clients/UserLink', $log['user'])
                ->isNullable(true)
                ->setDisposition('transitive');
        });
    }
}