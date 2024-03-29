<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\schedule;

use DecodeLabs\Tagged as Html;
use df\arch;
use df\core;

use df\halo;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    public const TITLE = 'Scheduled tasks';
    public const ICON = 'calendar';
    public const ADAPTER = 'axis://task/Schedule';
    public const NAME_FIELD = 'request';

    public const SECTIONS = [
        'details',
        'logs' => 'log'
    ];

    public const LIST_FIELDS = [
        'request', 'priority', 'creationDate', 'lastRun', 'lastTrigger', 'nextRun',
        'schedule', 'isLive', 'isAuto', 'status'
    ];

    public const DETAILS_FIELDS = [
        'id', 'request', 'priority', 'creationDate',
        'lastRun', 'lastRun', 'minute', 'hour', 'day', 'month', 'weekday',
        'isLive', 'isAuto'
    ];


    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query
            ->correlate('status')
                ->from('axis://task/Log', 'log')
                ->on('log.request', '=', 'schedule.request')
                ->orderBy('startDate DESC')
                ->limit(1)
                ->endCorrelation();
    }

    protected function countSectionItems($schedule): array
    {
        return [
            'logs' => $this->data->task->log->select()
                ->where('request', 'begins', $this->_normalizeRequest($schedule['request']))
                ->count()
        ];
    }


    // Sections
    public function renderLogsSectionBody($schedule)
    {
        return $this->apex->scaffold('../logs/')
            ->renderRecordList(function ($query) use ($schedule) {
                $query->where('request', 'begins', $this->_normalizeRequest($schedule['request']));
            });
    }

    protected function _normalizeRequest($request)
    {
        $request = arch\Request::factory($request);
        return (string)$request->path;
    }


    // Components
    public function generateRecordOperativeLinks(array $schedule): iterable
    {
        // Launch
        yield 'launch' => $this->html->link(
            $this->getRecordUri($schedule, 'launch', null, true),
            $this->_('Launch ' . $this->getRecordItemName())
        )
            ->setIcon('launch')
            ->setDisposition('positive');

        // Defaults
        yield from parent::generateRecordOperativeLinks($schedule);
    }



    public function generateIndexSubOperativeLinks(): iterable
    {
        $remote = halo\daemon\Remote::factory('TaskSpool');
        $isRunning = $remote->isRunning();

        yield 'nudge' => $this->html->link(
            $this->uri('~devtools/processes/schedule/nudge', true),
            $isRunning ?
                $this->_('Daemon is running') :
                $this->_('Launch spool daemon')
        )
            ->setIcon('launch')
            ->setDisposition('positive')
            ->isDisabled($isRunning);

        yield 'scan' => $this->html->link(
            $this->uri('~devtools/processes/schedule/scan', true),
            $this->_('Scan for tasks')
        )
            ->setIcon('search')
            ->setDisposition('operative');

        yield 'spool' => $this->html->link(
            $this->uri('~devtools/processes/queue/spool', true),
            $this->_('Spool now')
        )
            ->setIcon('launch')
            ->setDisposition('operative');
    }

    // Fields
    public function defineLastRunField($list, $mode)
    {
        $list->addField('lastRun', $this->_('Last run'), function ($schedule) {
            return Html::$time->since($schedule['lastRun']);
        });
    }

    public function defineLastTriggerField($list, $mode)
    {
        $list->addField('lastTrigger', function ($schedule) {
            if (!$schedule['isLive']) {
                return;
            }

            return Html::$time->since(core\time\Schedule::factory($schedule)->getLast(null, 1));
        });
    }

    public function defineNextRunField($list, $mode)
    {
        $list->addField('nextRun', function ($schedule) {
            if (!$schedule['isLive']) {
                return;
            }

            return Html::$time->untilAbs(core\time\Schedule::factory($schedule)->getNext(null, 1));
        });
    }

    public function defineScheduleField($list, $mode)
    {
        $list->addField('schedule', function ($schedule) {
            return
                $schedule['minute'] . ' ' .
                $schedule['hour'] . ' ' .
                $schedule['day'] . ' ' .
                $schedule['month'] . ' ' .
                $schedule['weekday'];
        });
    }

    public function defineIsAutoField($list, $mode)
    {
        $list->addField('isAuto', $this->_('Auto'), function ($schema) {
            return $this->html->lockIcon(!$schema['isAuto'])
                ->addClass($schema['isAuto'] ? 'positive' : 'negative');
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
