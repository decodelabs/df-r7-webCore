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
        'seen', 'lastSeen', 'bots'
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
            'logs' => $miss->missLogs->countAll()
        ];
    }

// Sections
    public function renderDetailsSectionBody($miss) {
        return [
            parent::renderDetailsSectionBody($miss),

            $this->apex->scaffold('./logs/')
                ->renderRecordList($miss->missLogs->select())
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

    public function addIndexOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link($this->uri('./purge', true), $this->_('Purge old logs'))
                ->setIcon('delete')
        );
    }


// Fields
    public function defineModeField($list, $mode) {
        $list->addField('mode', function($miss) {
            return $this->format->name($miss['mode']);
        });
    }

    public function defineRequestField($list, $mode) {
        return $this->apex->scaffold('../')->defineRequestField($list, $mode);
    }

    public function defineSeenField($list, $mode) {
        $list->addField('seen', function($miss) {
            $output = $this->html('span', $this->_(
                [
                    'n == 1' => '%n% time',
                    '*' => '%n% times'
                ],
                ['%n%' => $miss['seen']],
                $miss['seen']
            ));

            if($miss['seen'] > 100) {
                $output->addClass('priority-critical');
            } else if($miss['seen'] > 50) {
                $output->addClass('priority-high');
            } else if($miss['seen'] > 20) {
                $output->addClass('priority-medium');
            } else if($miss['seen'] > 5) {
                $output->addClass('priority-low');
            } else {
                $output->addClass('priority-trivial');
            }

            return $output;
        });
    }

    public function defineLastSeenField($list, $mode) {
        $list->addField('lastSeen', function($miss, $context) use($mode) {
            if($mode == 'list' && $miss['archiveDate']) {
                $context->getRowTag()->addClass('disabled');
            }

            $output = $this->html->timeFromNow($miss['lastSeen']);

            if($miss['lastSeen']->gt('-1 day')) {
                $output->addClass('priority-critical');
            } else if($miss['lastSeen']->gt('-3 days')) {
                $output->addClass('priority-high');
            } else if($miss['lastSeen']->gt('-1 week')) {
                $output->addClass('priority-medium');
            } else if($miss['lastSeen']->gt('-2 weeks')) {
                $output->addClass('priority-low');
            } else {
                $output->addClass('priority-trivial');
            }

            return $output;
        });
    }

    public function defineBotsField($list, $mode) {
        $list->addField('botsSeen', $this->_('Bots'), function($miss) {
            $percent = (100 / $miss['seen']) * $miss['botsSeen'];
            $output = $this->format->percent($percent);

            if($percent > 0) {
                $output = $this->html->icon('warning', $output);
                
                if($percent >= 50) {
                    $output->addClass('error');
                } else {
                    $output->addClass('warning');
                }
            }

            return $output;
        });
    }
}