<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\pestControl\errors;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DIRECTORY_TITLE = 'Critical errors';
    const DIRECTORY_ICON = 'error';
    const RECORD_ADAPTER = 'axis://pestControl/Error';
    const RECORD_NAME_KEY = 'message';
    const CAN_ADD_RECORD = false;
    const CAN_EDIT_RECORD = false;

    protected $_recordListFields = [
        'message', 'type', 'code', 'file', 'line', 
        'seen', 'lastSeen', 'actions'
    ];

    protected $_recordDetailsFields = [
        'id', 'type', 'file', 'line', 'code', 'message', 
        'seen', 'lastSeen'
    ];


// Record data
    public function getRecordOperativeLinks($record, $mode) {
        return array_merge(
            [
                $this->html->link(
                        $this->_getRecordActionRequest($record, 'archive', null, true),
                        $this->_('Archive '.$this->getRecordItemName())
                    )
                    ->setIcon('remove')
                    ->isDisabled(isset($record['archiveDate']))
            ],
            parent::getRecordOperativeLinks($record, $mode)
        );
    }

    public function applyRecordQuerySearch(opal\query\ISelectQuery $query, $search, $mode) {
        $query->beginWhereClause()
            ->where('id', '=', ltrim($search, '#'))
            ->orWhere('type', 'matches', $search)
            ->orWhere('message', 'matches', $search)
            ->endClause();
    }

    protected function _fetchSectionItemCounts() {
        $error = $this->getRecord();
        $model = $this->getRecordAdapter()->getModel();

        return [
            'logs' => $error->errorLogs->select()->count(),
            /*
            'stackTraces' => $model->stackTrace->select()
                ->whereCorrelation('id', 'in', 'stackTrace')
                    ->from($model->errorLog, 'log')
                    ->where('error', '=', $error)
                    ->endCorrelation()
                ->count()
            */
        ];
    }


// Sections
    public function renderDetailsSectionBody($error) {
        $logList = $error->errorLogs->select()
            ->importRelationBlock('error', 'list')
            ->paginateWith($this->request->query);


        return [
            parent::renderDetailsSectionBody($error),
            
            $this->import->component('~admin/system/pestControl/errors/logs/LogList')
                ->setCollection($logList)
                ->setUrlRedirect(true)
        ];
    }

// Fields
    public function defineTypeField($list, $mode) {
        $list->addField('type', function($error) use($mode) {
            if(!$output = $error['type']) {
                return $output;
            }

            if($mode == 'list') {
                $output = $this->format->shorten($output, 35);
            }

            $output = $this->html->element('code', $output);

            if($mode == 'list') {
                $output->setAttribute('title', $error['type']);
            }

            return $output;
        });
    }

    public function defineFileField($list, $mode) {
        $list->addField('file', function($error) use($mode) {
            $output = $error['file'];

            if($mode == 'list') {
                $output = $this->format->shorten($output, 35, true);
            }

            $output = $this->html->element('code', $output.' : '.$error['line']);

            if($mode == 'list') {
                $output->setAttribute('title', $error['file']);
            }

            return $output;
        });
    }

    public function defineLineField($list, $mode) {
        $list->addLabel('file', 'line');
    }

    public function defineMessageField($list, $mode) {
        if($mode == 'list') {
            return false;
        }

        $list->addField('message', function($error) {
            return $this->html->element('samp', $error['message']);
        });
    }

    public function defineSeenField($list, $mode) {
        $list->addField('seen', function($error) {
            $output = $this->html->element('span', $this->_(
                [
                    'n == 1' => '%n% time',
                    '*' => '%n% times'
                ],
                ['%n%' => $error['seen']],
                $error['seen']
            ));

            if($error['seen'] > 100) {
                $output->addClass('priority-critical');
            } else if($error['seen'] > 50) {
                $output->addClass('priority-high');
            } else if($error['seen'] > 20) {
                $output->addClass('priority-medium');
            } else if($error['seen'] > 5) {
                $output->addClass('priority-low');
            } else {
                $output->addClass('priority-trivial');
            }

            return $output;
        });
    }

    public function defineLastSeenField($list, $mode) {
        $list->addField('lastSeen', function($error, $context) use($mode) {
            if($mode == 'list' && $error['archiveDate']) {
                $context->getRowTag()->addClass('disabled');
            }

            $output = $this->html->timeFromNow($error['lastSeen']);

            if($error['lastSeen']->gt('-1 day')) {
                $output->addClass('priority-critical');
            } else if($error['lastSeen']->gt('-3 days')) {
                $output->addClass('priority-high');
            } else if($error['lastSeen']->gt('-1 week')) {
                $output->addClass('priority-medium');
            } else if($error['lastSeen']->gt('-2 weeks')) {
                $output->addClass('priority-low');
            } else {
                $output->addClass('priority-trivial');
            }

            return $output;
        });
    }
}