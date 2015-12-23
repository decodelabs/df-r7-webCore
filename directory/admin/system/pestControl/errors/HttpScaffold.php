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

class HttpScaffold extends arch\scaffold\RecordAdmin {

    const TITLE = 'Critical errors';
    const ICON = 'error';
    const ADAPTER = 'axis://pestControl/Error';
    const NAME_FIELD = 'message';
    const CAN_ADD = false;
    const CAN_EDIT = false;

    protected $_recordListFields = [
        'message', 'type', 'code', 'file', 'line',
        'seen', 'lastSeen'
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
                        $this->_getRecordNodeRequest($record, 'archive', null, true),
                        $this->_('Archive '.$this->getRecordItemName())
                    )
                    ->setIcon('remove')
                    ->isDisabled(isset($record['archiveDate']))
            ],
            parent::getRecordOperativeLinks($record, $mode)
        );
    }


// Sections
    public function renderDetailsSectionBody($error) {
        $logList = $error->errorLogs->select()
            ->importRelationBlock('error', 'list')
            ->importRelationBlock('user', 'link')
            ->paginateWith($this->request->query);


        return [
            parent::renderDetailsSectionBody($error),

            $this->apex->component('./logs/LogList')
                ->setCollection($logList)
                ->setUrlRedirect(true)
        ];
    }



// Components
    public function addIndexSectionLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link('./', $this->_('Errors'))
                ->setIcon('error')
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
    public function defineTypeField($list, $mode) {
        $list->addField('type', function($error) use($mode) {
            if(!$output = $error['type']) {
                return $output;
            }

            if($mode == 'list') {
                $output = $this->format->shorten($output, 35);
            }

            $output = $this->html('code', $output);

            if($mode == 'list') {
                $output->setTitle($error['type']);
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

            $output = $this->html('code', $output.' : '.$error['line']);

            if($mode == 'list') {
                $output->setTitle($error['file']);
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
            return $this->html('samp', $error['message']);
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