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
        'date', 'name', 'email', 'user', 'environmentMode', 'expireDate'
    ];

    protected $_recordDetailsFields = [
        'date', 'name', 'email', 'user', 'environmentMode', 'expireDate',
        'objectId1', 'objectId2'
    ];


    public function fixNamesAction() {
        $list = $this->data->mail->journal->fetch()
            ->where('name', 'begins', '~mail/');

        $count = 0;

        foreach($list as $mail) {
            $mail['name'] = substr($mail['name'], 6);
            $mail->save();
            $count++;
        }

        core\dump($count);
    }

    public function fixDurationsAction() {
        $list = $this->data->mail->journal->fetch();
        $count = 0;

        foreach($list as $mail) {
            $date = $mail['date'];

            try {
                $component = $this->directory->getComponent('~mail/'.$mail['name']);

                if(!$component instanceof arch\IMailComponent) {
                    continue;
                }
            } catch(\Exception $e) {
                continue;
            }

            $date = $date->modifyNew('+'.$component::JOURNAL_WEEKS.' weeks');
            $mail->expireDate = $date;
            $mail->save();
            $count++;
        }

        core\dump($count);
    }

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
            return $this->import->component('~admin/users/clients/UserLink', $log['user'])
                ->isNullable(true)
                ->setDisposition('transitive');
        });
    }

    public function defineExpireDateField($list, $mode) {
        $list->addField('expireDate', $this->_('Expires'), function($log) {
            return $this->html->timeFromNow($log['expireDate']);
        });
    }
}