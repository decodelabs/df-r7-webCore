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

    protected $_recordListFields = [
        'date', 'name', 'email', 'user', 'environmentMode', 'expireDate', 'actions' => false
    ];

    protected $_recordDetailsFields = [
        'date', 'name', 'email', 'user', 'environmentMode', 'expireDate',
        'objectId1', 'objectId2'
    ];


    public function fixNamesNode() {
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

    public function fixDurationsNode() {
        $list = $this->data->mail->journal->fetch();
        $count = 0;

        foreach($list as $mail) {
            $date = $mail['date'];

            try {
                $component = $this->apex->component('~mail/'.$mail['name']);

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