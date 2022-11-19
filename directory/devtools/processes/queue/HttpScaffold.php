<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\queue;

use DecodeLabs\Tagged as Html;

use df\arch;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    public const TITLE = 'Task queue';
    public const ICON = 'task';
    public const ADAPTER = 'axis://task/Queue';
    public const NAME_FIELD = 'request';
    public const KEY_NAME = 'task';

    public const LIST_FIELDS = [
        'request', 'priority', 'queueDate',
        'lockDate', 'status'
    ];

    public const DETAILS_FIELDS = [
        'id', 'request', 'priority', 'queueDate',
        'lockDate', 'lockId', 'logs', 'status'
    ];


    // Components
    public function generateRecordOperativeLinks(array $task): iterable
    {
        // Launch
        yield 'launch' => $this->html->link(
            $this->uri('~devtools/processes/queue/launch?task=' . $task['id'], true),
            $this->_('Launch now')
        )
            ->setIcon('launch')
            ->setDisposition('positive');

        // Defaults
        yield from parent::generateRecordOperativeLinks($task);
    }

    public function generateIndexSubOperativeLinks(): iterable
    {
        yield 'spool' => $this->html->link(
            $this->uri('~devtools/processes/queue/spool', true),
            $this->_('Run spool now')
        )
            ->setIcon('launch')
            ->setDisposition('operative');
    }

    // Fields
    public function defineQueueDateField($list, $mode)
    {
        $list->addField('queueDate', $this->_('Queued'), function ($log) {
            return Html::$time->since($log['queueDate']);
        });
    }

    public function defineLockDateField($list, $mode)
    {
        $list->addField('lockDate', $this->_('Locked'), function ($log) {
            return Html::$time->since($log['lockDate']);
        });
    }

    public function defineLogsField($list, $mode)
    {
        $list->addField('logs', $this->_('Previous launches'), function ($log) {
            $request = new arch\Request($log['request']);
            $request->setQuery(null);
            $request = (string)$request->getPath();

            $count = $this->data->task->log->select('id')
                ->where('request', 'matches', $request)
                ->count();

            if (!$count) {
                return;
            }

            return $this->html->link(
                $this->uri('~devtools/processes/logs/?search=' . $request, true),
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

    public function defineStatusField($list, $mode)
    {
        $list->addField('status', function ($log) {
            switch ($log['status']) {
                case 'pending':
                    return $this->html->icon('time', 'Pending')->addClass('warning');

                case 'locked':
                    return $this->html->icon('lock', 'Locked')->addClass('warning');

                case 'processing':
                    return $this->html->icon('time', 'Processing')->addClass('positive');

                case 'lagging':
                    return $this->html->icon('warning', 'Lagging')->addClass('negative');

                case 'complete':
                    return $this->html->icon('tick', 'Complete')->addClass('positive');
            }
        });
    }
}
