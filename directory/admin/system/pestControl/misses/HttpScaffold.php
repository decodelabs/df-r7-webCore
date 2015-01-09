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
    const RECORD_NAME_FIELD = 'id';
    const CAN_ADD_RECORD = false;
    const CAN_EDIT_RECORD = false;

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

    protected function _fetchSectionItemCounts() {
        $miss = $this->getRecord();

        return [
            'logs' => $miss->missLogs->select()->count()
        ];
    }

// Sections
    public function renderDetailsSectionBody($miss) {
        $logList = $miss->missLogs->select()
            ->importRelationBlock('miss', 'list')
            ->importRelationBlock('user', 'link')
            ->paginateWith($this->request->query);

        return [
            parent::renderDetailsSectionBody($miss),

            $this->apex->component('./logs/LogList')
                ->setCollection($logList)
                ->setUrlRedirect(true)
        ];
    }


// Components
    public function addIndexSectionLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link('./', $this->_('URLs'))
                ->setIcon('brokenLink')
                ->setDisposition('informative')
                ->isActive(true),

            $this->html->link('./logs/', $this->_('Logs'))
                ->setIcon('log')
                ->setDisposition('informative')
        );
    }


// Fields
    public function defineModeField($list, $mode) {
        $list->addField('mode', function($miss) {
            return $this->format->name($miss['mode']);
        });
    }

    public function defineRequestField($list, $mode) {
        $list->addField('request', function($miss, $context) use($mode) {
            if(!$request = $miss['request']) return;
            $context->getCellTag()->setStyle('word-break', 'break-all');
            $output = $this->uri->directoryRequest($request);

            if($mode == 'list') {
                unset($output->query->rf, $output->query->rt);
                $output = $this->format->shorten($output->toReadableString(), 60, true);
            }

            $output = $this->html('code', $output);

            if($mode == 'list') {
                $output->setTitle($request);
            }

            if($miss['mode'] == 'Http') {
                $output = $this->html->link($request, $output)
                    ->setIcon('link')
                    ->setDisposition('transitive')
                    ->setTarget('_blank');
            }

            return $output;
        });
    }

    public function defineSeenField($list, $mode) {
        $list->addField('seen', function($error) {
            $output = $this->html('span', $this->_(
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