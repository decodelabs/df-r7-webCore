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

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;

    const DIRECTORY_TITLE = 'Scheduled tasks';
    const DIRECTORY_ICON = 'calendar';
    const RECORD_ADAPTER = 'axis://task/Schedule';
    const RECORD_NAME_KEY = 'request';

    protected $_recordListFields = [
        'request', 'environmentMode', 'priority', 
        'creationDate', 'lastRun', 'schedule', 'isLive', 'actions'
    ];

    protected $_recordDetailsFields = [
        'id', 'request', 'environmentMode', 'priority', 'creationDate',
        'lastRun', 'minute', 'hour', 'day', 'month', 'weekday', 'isLive'
    ];


// Components
    public function addIndexSubOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~devtools/tasks/schedule/scan', true),
                    $this->_('Scan for new tasks')
                )
                ->setIcon('search')
                ->setDisposition('operative'),

            $this->html->link(
                    $this->uri->request('~devtools/tasks/queue/spool', true),
                    $this->_('Run spool now')
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
}