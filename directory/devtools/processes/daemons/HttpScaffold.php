<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\devtools\processes\daemons;

use DecodeLabs\Dictum;
use DecodeLabs\R7\Config\Environment as EnvironmentConfig;
use DecodeLabs\Tagged as Html;
use df\arch;
use df\core;
use df\halo;
use df\opal;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;
    public const TITLE = 'Daemons';
    public const ICON = 'launch';
    public const KEY_NAME = 'daemon';
    public const ID_FIELD = 'name';
    public const NAME_FIELD = 'name';
    public const CAN_ADD = false;
    public const CAN_EDIT = false;
    public const CAN_DELETE = false;

    public const LIST_FIELDS = [
        'name', 'state', 'startDate', 'statusDate',
        'pid', 'testMode', 'automatic'
    ];

    public const DETAILS_FIELDS = [
        'name', 'state', 'startDate', 'statusDate',
        'pid', 'user', 'group', 'testMode', 'automatic'
    ];

    protected $_enabled;


    // Record data
    protected function generateRecordAdapter()
    {
        $this->_enabled = EnvironmentConfig::load()->canUseDaemons();

        $daemons = halo\daemon\Base::loadAll();
        $data = [];
        $settings = $this->data->daemon->settings->select()->toKeyArray('name');

        foreach ($daemons as $name => $daemon) {
            $remote = halo\daemon\Remote::factory($daemon);
            $status = $remote->getStatusData();

            $daemonSettings = $settings[$name] ?? $this->data->newRecord('axis://daemon/Settings')->toArray();

            $row = [
                'name' => $name,
                'isEnabled' => $daemonSettings['isEnabled'],
                'isRunning' => $remote->isRunning(),
                'startDate' => $status ? new core\time\Date($status['startTime']) : null,
                'statusDate' => $status ? new core\time\Date($status['statusTime']) : null,
                'state' => $status ? $status['state'] : 'stopped',
                'pid' => $status ? $status['pid'] : null,
                'user' => $daemonSettings['user'],
                'group' => $daemonSettings['group'],
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
    public function generateRecordOperativeLinks(array $daemon): iterable
    {
        if ($daemon['isRunning']) {
            // Restart
            yield 'restart' => $this->html->link(
                $this->uri('~devtools/processes/daemons/restart?daemon=' . $daemon['name'], true),
                $this->_('Restart daemon')
            )
                ->setIcon('refresh')
                ->setDisposition('operative')
                ->isDisabled(!$this->_enabled);

            // Stop
            yield 'stop' => $this->html->link(
                $this->uri('~devtools/processes/daemons/stop?daemon=' . $daemon['name'], true),
                $this->_('Stop daemon')
            )
                ->setIcon('remove')
                ->setDisposition('negative')
                ->isDisabled(!$this->_enabled);
        } else {
            // Start
            yield 'start' => $this->html->link(
                $this->uri('~devtools/processes/daemons/start?daemon=' . $daemon['name'], true),
                $this->_('Start daemon')
            )
                ->setIcon('launch')
                ->setDisposition('positive')
                ->isDisabled(!$this->_enabled || !$daemon['isEnabled'] || $daemon['testMode']);
        }
    }

    public function generateIndexSubOperativeLinks(): iterable
    {
        yield 'settings' => $this->html->link(
            $this->uri('~devtools/processes/daemons/settings', true),
            $this->_('Settings')
        )
            ->setIcon('settings')
            ->setDisposition('operative');
    }

    // Fields
    public function defineStateField($list, $mode)
    {
        $list->addField('state', function ($daemon, $context) {
            if (!$daemon['isEnabled']) {
                $context->getRowTag()->addClass('disabled');
            }

            switch ($daemon['state']) {
                case 'running': $class = 'positive';
                    break;
                case 'stopped': $class = 'negative';
                    break;
                case 'stopping':
                case 'paused':
                default: $class = 'warning';
                    break;
            }

            return Html::{'span.' . $class}(Dictum::name($daemon['state']));
        });
    }

    public function defineStartDateField($list, $mode)
    {
        $list->addField('startDate', $this->_('Launched'), function ($daemon) {
            return Html::$time->since($daemon['startDate']);
        });
    }

    public function defineStatusDateField($list, $mode)
    {
        $list->addField('statusDate', $this->_('Last status'), function ($daemon) {
            return Html::$time->since($daemon['statusDate']);
        });
    }

    public function defineTestModeField($list, $mode)
    {
        $list->addField('testMode', $this->_('Test'), function ($daemon, $context) {
            if ($daemon['testMode']) {
                $context->getRowTag()->addClass('inactive');
            }

            return $this->html->booleanIcon($daemon['testMode']);
        });
    }

    public function defineAutomaticField($list, $mode)
    {
        $list->addField('automatic', $this->_('Auto'), function ($daemon) {
            return $this->html->booleanIcon($daemon['automatic']);
        });
    }

    public function defineUserField($list, $mode)
    {
        $list->addField('user');
    }
}
