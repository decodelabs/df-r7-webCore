<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\pestControl\misses;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DIRECTORY_TITLE = '404 errors';
    const DIRECTORY_ICON = 'brokenLink';
    const RECORD_ADAPTER = 'axis://pestControl/Miss';
    const RECORD_ITEM_NAME = '404 error';
    const RECORD_NAME_KEY = 'id';
    const CAN_ADD_RECORD = false;
    const CAN_EDIT_RECORD = false;

    protected $_sections = [
        'details',
        'logs' => [
            'icon' => 'log'
        ]
    ];

    protected $_recordListFields = [
        'id', 'mode', 'request', 
        'seen', 'lastSeen', 'actions'
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
            ->orWhere('mode', 'matches', $search)
            ->orWhere('request', 'matches', $search)
            ->endClause();
    }

    protected function _fetchSectionItemCounts() {
        $miss = $this->getRecord();

        return [
            'logs' => $miss->missLogs->select()->count()
        ];
    }

// Sections
    public function renderLogsSectionBody($miss) {
        $logList = $miss->missLogs->select()
            ->importRelationBlock('miss', 'list')
            ->importRelationBlock('user', 'link')
            ->paginateWith($this->request->query);

        return $this->import->component('~admin/system/pestControl/misses/logs/LogList')
            ->setCollection($logList)
            ->setUrlRedirect(true);
    }

// Fields
    public function defineModeField($list, $mode) {
        $list->addField('mode', function($log) {
            return $this->format->name($log['mode']);
        });
    }

    public function defineRequestField($list, $mode) {
        $list->addField('request', function($log) use($mode) {
            if(!$request = $log['request']) return;
            $output = $request;
            $link = false;

            if(substr($request, 0, 4) == 'http') {
                $output = $this->normalizeOutputUrl($output);

                if($mode == 'list') {
                    $output = $this->format->shorten((string)$output->getPath(), 40, true);
                }
            } else if(substr($request, 0, 9) == 'directory') {
                $output = $this->directory->newRequest($request);

                if($mode == 'list') {
                    $output = $this->format->shorten((string)$output->getPath(), 40, true);
                }
            } else if($mode == 'list') {
                $output = $this->format->shorten($output, 40, true);
            }

            $output = $this->html->element('code', $output);

            if($mode == 'list') {
                $output->setAttribute('title', $request);
            }

            if($link) {
                $output = $this->html->link($request, $output)
                    ->setIcon('link')
                    ->setTarget('_blank');
            }

            return $output;
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