<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\devtools\processes\logs;

use DecodeLabs\Tagged as Html;

use df\arch;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const DEFAULT_ACCESS = arch\IAccess::DEV;

    public const TITLE = 'Spool logs';
    public const ICON = 'log';
    public const ADAPTER = 'axis://task/Log';
    public const NAME_FIELD = 'request';
    public const CAN_ADD = false;
    public const CAN_EDIT = false;

    public const LIST_FIELDS = [
        'request', 'startDate', 'lastActivity', 'runTime',
        'status', 'environmentMode'
    ];

    public const DETAILS_FIELDS = [
        'id', 'request', 'environmentMode', 'startDate', 'runTime',
        'status'
    ];

    public const SEARCH_FIELDS = [
        'request' => 10
    ];


    // Sections
    public function renderDetailsSectionBody($log)
    {
        $output = [parent::renderDetailsSectionBody($log)];

        if ($log['errorOutput']) {
            $output[] = [
                Html::{'h3'}($this->_('Error output')),
                Html::{'samp.terminal-output.error'}($log['errorOutput'])
            ];
        }

        if ($log['output']) {
            $output[] = [
                Html::{'h3'}($this->_('Standard output')),
                Html::{'samp.terminal-output'}($log['output'])
            ];
        }

        return $output;
    }


    // Components
    public function generateIndexOperativeLinks(): iterable
    {
        yield 'deleteAll' => $this->html->link(
            $this->uri('~devtools/processes/logs/delete-all', true),
            $this->_('Delete all logs')
        )
            ->setIcon('delete');
    }


    // Fields
    public function defineStartDateField($list, $mode)
    {
        $list->addField('startDate', $this->_('Started'), function ($log) {
            return Html::$time->since($log['startDate']);
        });
    }

    public function defineLastActivityField($list, $mode)
    {
        $list->addField('lastActivity', 'Activity', function ($log) {
            return Html::$time->since($log['lastActivity']);
        });
    }

    public function defineRunTimeField($list, $mode)
    {
        $list->addField('runTime', function ($log) {
            return $this->date->formatDuration($log['runTime']);
        });
    }

    public function defineStatusField($list, $mode)
    {
        $list->addField('status', function ($log) {
            if ($log['status']) {
                $output = $this->data->task->status->label($log['status']);
            } else {
                $output = null;
            }


            if ($log['errorOutput']) {
                return $this->html->icon('error', $output ?? 'Error')
                    ->addClass('error');
            } elseif (!$log['output']) {
                return $this->html->icon('warning', $output ?? 'No output')
                    ->addClass('warning');
            } else {
                return $this->html->icon('tick', $output ?? 'Success')
                    ->addClass('success');
            }
        });
    }
}
