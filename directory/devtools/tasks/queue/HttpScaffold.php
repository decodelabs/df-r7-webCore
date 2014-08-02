<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\tasks\queue;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    
    const DIRECTORY_TITLE = 'Task queue';
    const DIRECTORY_ICON = 'task';
    const RECORD_ADAPTER = 'axis://task/Queue';
    const RECORD_NAME_KEY = 'request';
    const RECORD_KEY_NAME = 'task';

    protected $_recordListFields = [
        'request', 'environmentMode', 'priority', 'queueDate',
        'lockDate', 'actions'
    ];

    protected $_recordDetailsFields = [
        'id', 'request', 'environmentMode', 'priority', 'queueDate',
        'lockDate', 'lockId', 'logs'
    ];

// Record data
    public function getRecordOperativeLinks($task, $mode) {
        return array_merge(
            [
                $this->html->link(
                        $this->uri->request('~devtools/tasks/queue/launch?task='.$task['id'], true),
                        $this->_('Launch now')
                    )
                    ->setIcon('launch')
                    ->setDisposition('positive')
            ],
            parent::getRecordOperativeLinks($task, $mode)
        );
    }

// Components
    public function addIndexSubOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~devtools/tasks/queue/spool', true),
                    $this->_('Run spool now')
                )
                ->setIcon('launch')
                ->setDisposition('operative')
        );
    }

// Fields
    public function defineQueueDateField($list, $mode) {
        $list->addField('queueDate', $this->_('Queued'), function($log) {
            return $this->html->timeFromNow($log['queueDate']);
        });
    }

    public function defineLockDateField($list, $mode) {
        $list->addField('lockDate', $this->_('Locked'), function($log) {
            return $this->html->timeFromNow($log['lockDate']);
        });
    }

    public function defineLogsField($list, $mode) {
        $list->addField('logs', $this->_('Previous launches'), function($log) {
            $request = new arch\Request($log['request']);
            $request->setQuery(null);
            $request = (string)$request->getPath();

            $count = $this->data->task->log->select('id')
                ->where('request', 'matches', $request)
                ->count();

            if(!$count) {
                return;
            }

            return $this->html->link(
                    $this->uri->request('~devtools/tasks/logs/?search='.$request, true),
                    $this->_(
                        [
                            'n == 1' => 'View 1 log',
                            'n > 1' => 'View %c% logs'
                        ],
                        ['%c%' => $count],
                        $count
                    )
                )
                ->setIcon('log');
        });
    }
}