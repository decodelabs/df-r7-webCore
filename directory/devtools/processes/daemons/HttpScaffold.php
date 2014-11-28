<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\daemons;

use df;
use df\core;
use df\apex;
use df\arch;
use df\halo;
use df\opal;

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DEFAULT_ACCESS = arch\IAccess::DEV;
    const DIRECTORY_TITLE = 'Daemons';
    const DIRECTORY_ICON = 'launch';
    const RECORD_KEY_NAME = 'daemon';
    const RECORD_ID_KEY = 'name';
    const RECORD_NAME_KEY = 'name';
    const CAN_ADD_RECORD = false;
    const CAN_EDIT_RECORD = false;
    const CAN_DELETE_RECORD = false;

    protected $_recordListFields = [
        'name', 'state', 'startDate', 'statusDate', 
        'pid', 'testMode', 'automatic', 'actions'
    ];

    protected $_enabled;


// Record data
    protected function _generateRecordAdapter() {
        $this->_enabled = core\Environment::getInstance()->canUseDaemons();

        $daemons = halo\daemon\Base::loadAll();
        $data = [];

        foreach($daemons as $name => $daemon) {
            $remote = halo\daemon\Remote::factory($daemon);
            $status = $remote->getStatusData();

            $row = [
                'name' => $name,
                'isRunning' => $remote->isRunning(),
                'startDate' => $status ? new core\time\Date($status['startTime']) : null,
                'statusDate' => $status ? new core\time\Date($status['statusTime']) : null,
                'state' => $status ? $status['state'] : 'stopped',
                'pid' => $status ? $status['pid'] : null,
                'testMode' => $daemon::TEST_MODE,
                'automatic' => $daemon::AUTOMATIC,
                '@daemon' => $daemon,
                '@remote' => $remote
            ];

            $data[] = $row;
        }

        return new opal\native\QuerySourceAdapter('daemons', $data, 'name');
    }


// Components
    public function getRecordOperativeLinks($daemon, $mode) {
        $output = [];

        if($daemon['isRunning']) {
            return [
                $this->html->link(
                        $this->uri('~devtools/processes/daemons/restart?daemon='.$daemon['name'], true),
                        $this->_('Restart daemon')
                    )
                    ->setIcon('refresh')
                    ->setDisposition('operative')
                    ->isDisabled(!$this->_enabled),

                $this->html->link(
                        $this->uri('~devtools/processes/daemons/stop?daemon='.$daemon['name'], true),
                        $this->_('Stop daemon')
                    )
                    ->setIcon('remove')
                    ->setDisposition('negative')
                    ->isDisabled(!$this->_enabled)
            ];  
        } else {
            return [
                $this->html->link(
                        $this->uri('~devtools/processes/daemons/start?daemon='.$daemon['name'], true),
                        $this->_('Start daemon')
                    )
                    ->setIcon('launch')
                    ->setDisposition('positive')
                    ->isDisabled(!$this->_enabled || $daemon['testMode'])
            ];  
        }
    }

    public function addIndexSubOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri('~devtools/processes/daemons/settings', true),
                    $this->_('Settings')
                )
                ->setIcon('settings')
                ->setDisposition('operative')
        );
    }

// Fields
    public function defineStateField($list, $mode) {
        $list->addField('state', function($daemon, $context) {
            if(!$daemon['isRunning']) {
                $context->getRowTag()->addClass('disabled');
            }

            switch($daemon['state']) {
                case 'running': $class = 'positive'; break;
                case 'stopping':
                case 'paused': $class = 'warning'; break;
                case 'stopped': $class = 'negative'; break;
            }

            return $this->html('span.'.$class, $this->format->name($daemon['state']));
        });
    }

    public function defineStartDateField($list, $mode) {
        $list->addField('startDate', $this->_('Launched'), function($daemon) {
            return $this->html->timeFromNow($daemon['startDate']);
        });
    }

    public function defineStatusDateField($list, $mode) {
        $list->addField('statusDate', $this->_('Last status'), function($daemon) {
            return $this->html->timeFromNow($daemon['statusDate']);
        });
    }

    public function defineTestModeField($list, $mode) {
        $list->addField('testMode', $this->_('Test'), function($daemon, $context) {
            if($daemon['testMode']) {
                $context->getRowTag()->addClass('inactive');
            }

            return $this->html->booleanIcon($daemon['testMode']);
        });
    }

    public function defineAutomaticField($list, $mode) {
        $list->addField('automatic', $this->_('Auto'), function($daemon) {
            return $this->html->booleanIcon($daemon['automatic']);
        });
    }
}