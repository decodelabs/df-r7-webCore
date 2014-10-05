<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\tasks\schedule;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;
use df\halo;

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    const DIRECTORY_TITLE = 'Scheduled tasks';
    const DIRECTORY_ICON = 'calendar';
    const RECORD_ADAPTER = 'axis://task/Schedule';
    const RECORD_NAME_KEY = 'request';

    protected $_recordListFields = [
        'request', 'priority', 'creationDate', 'lastRun', 'lastTrigger', 'nextRun',
        'schedule', 'environmentMode', 'isLive', 'isAuto', 'actions'
    ];

    protected $_recordDetailsFields = [
        'id', 'request', 'priority', 'creationDate',
        'lastRun', 'lastRun', 'minute', 'hour', 'day', 'month', 'weekday', 
        'environmentMode', 'isLive', 'isAuto'
    ];


// Components
    public function addIndexSubOperativeLinks($menu, $bar) {
        $remote = halo\daemon\Remote::factory('TaskSpool');
        $isRunning = $remote->isRunning();

        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~devtools/tasks/schedule/nudge', true),
                    $isRunning ?
                        $this->_('Daemon is running') :
                        $this->_('Launch spool daemon')
                )
                ->setIcon('launch')
                ->setDisposition('positive')
                ->isDisabled($isRunning),

            $this->html->link(
                    $this->uri->request('~devtools/tasks/schedule/scan', true),
                    $this->_('Scan for tasks')
                )
                ->setIcon('search')
                ->setDisposition('operative'),

            $this->html->link(
                    $this->uri->request('~devtools/tasks/queue/spool', true),
                    $this->_('Spool now')
                )
                ->setIcon('launch')
                ->setDisposition('operative')
        );
    }

// Fields
    public function defineLastRunField($list, $mode) {
        $list->addField('lastRun', $this->_('Last run'), function($schedule) {
            return $this->html->timeFromNow($schedule['lastRun']);
        });
    }

    public function defineLastTriggerField($list, $mode) {
        $list->addField('lastTrigger', function($schedule) {
            if(!$schedule['isLive']) {
                return;
            }
            
            return $this->html->timeFromNow(core\time\Schedule::factory($schedule)->getLast(null, 1));
        });
    }

    public function defineNextRunField($list, $mode) {
        $list->addField('nextRun', function($schedule) {
            if(!$schedule['isLive']) {
                return;
            }
            
            return $this->html->timeFromNow(core\time\Schedule::factory($schedule)->getNext(null, 1));
        });
    }

    public function defineScheduleField($list, $mode) {
        $list->addField('schedule', function($schedule) {
            return 
                $schedule['minute'].' '.
                $schedule['hour'].' '.
                $schedule['day'].' '.
                $schedule['month'].' '.
                $schedule['weekday'];
        });
    }

    public function defineIsAutoField($list, $mode) {
        $list->addField('isAuto', $this->_('Auto'), function($schema) {
            return $this->html->lockIcon(!$schema['isAuto'])
                ->addClass($schema['isAuto'] ? 'positive' : 'negative');
        });
    }
}