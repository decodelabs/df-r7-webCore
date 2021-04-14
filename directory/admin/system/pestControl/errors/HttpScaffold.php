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

use DecodeLabs\Dictum;
use DecodeLabs\Tagged as Html;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    const TITLE = 'Critical errors';
    const ICON = 'error';
    const ADAPTER = 'axis://pestControl/Error';
    const NAME_FIELD = 'message';
    const CAN_ADD = false;
    const CAN_EDIT = false;

    const LIST_FIELDS = [
        'message', 'type', 'code', 'file', 'line',
        'seen', 'lastSeen'
    ];

    const DETAILS_FIELDS = [
        'id', 'type', 'file', 'line', 'code', 'message',
        'seen', 'lastSeen'
    ];

    const CAN_SELECT = true;


    // Sections
    public function renderDetailsSectionBody($error)
    {
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
    public function generateRecordOperativeLinks($error): iterable
    {
        // Archive
        yield 'archive' => $this->html->link(
                $this->getRecordUri($error, 'archive', null, true),
                $this->_('Archive '.$this->getRecordItemName())
            )
            ->setIcon('remove');

        // Defaults
        yield from parent::generateRecordOperativeLinks($error);
    }


    public function generateIndexOperativeLinks(): iterable
    {
        yield 'purge' => $this->html->link($this->uri('./purge', true), $this->_('Purge old logs'))
            ->setIcon('delete');

        yield 'purgeAll' => $this->html->link($this->uri('./purge-all', true), $this->_('Purge ALL'))
            ->setIcon('delete');
    }

    public function generateIndexSectionLinks(): iterable
    {
        yield 'index' => $this->html->link('./', $this->_('Errors'))
            ->setIcon('error')
            ->setDisposition('informative')
            ->isActive(true);

        yield 'logs' => $this->html->link('./logs/', $this->_('Logs'))
            ->setIcon('log')
            ->setDisposition('informative');
    }


    // Fields
    public function defineTypeField($list, $mode)
    {
        $list->addField('type', function ($error) use ($mode) {
            if (!$output = $error['type']) {
                return $output;
            }

            if ($mode == 'list') {
                $output = Dictum::shorten($output, 35);
            }

            $output = Html::{'code'}($output);

            if ($mode == 'list') {
                $output->setTitle($error['type']);
            }

            return $output;
        });
    }

    public function defineFileField($list, $mode)
    {
        $list->addField('file', function ($error) use ($mode) {
            $output = $error['file'];

            if ($mode == 'list') {
                $output = Dictum::shorten($output, 35, true);
            }

            $output = Html::{'code'}($output.' : '.$error['line']);

            if ($mode == 'list') {
                $output->setTitle($error['file']);
            }

            return $output;
        });
    }

    public function defineLineField($list, $mode)
    {
        $list->addLabel('file', 'line');
    }

    public function defineMessageField($list, $mode)
    {
        if ($mode == 'list') {
            return false;
        }

        $list->addField('message', function ($error) {
            return Html::{'samp'}($error['message']);
        });
    }

    public function defineSeenField($list, $mode)
    {
        $list->addField('seen', function ($error) {
            $output = Html::{'span'}($this->_(
                [
                    'n == 1' => '%n% time',
                    '*' => '%n% times'
                ],
                ['%n%' => $error['seen']],
                $error['seen']
            ));

            if ($error['seen'] > 100) {
                $output->addClass('priority-critical');
            } elseif ($error['seen'] > 50) {
                $output->addClass('priority-high');
            } elseif ($error['seen'] > 20) {
                $output->addClass('priority-medium');
            } elseif ($error['seen'] > 5) {
                $output->addClass('priority-low');
            } else {
                $output->addClass('priority-trivial');
            }

            return $output;
        });
    }

    public function defineLastSeenField($list, $mode)
    {
        $list->addField('lastSeen', function ($error, $context) use ($mode) {
            if ($mode == 'list' && $error['archiveDate']) {
                $context->getRowTag()->addClass('disabled');
            }

            $output = Html::$time->since($error['lastSeen']);

            if ($error['lastSeen']->gt('-1 day')) {
                $output->addClass('priority-critical');
            } elseif ($error['lastSeen']->gt('-3 days')) {
                $output->addClass('priority-high');
            } elseif ($error['lastSeen']->gt('-1 week')) {
                $output->addClass('priority-medium');
            } elseif ($error['lastSeen']->gt('-2 weeks')) {
                $output->addClass('priority-low');
            } else {
                $output->addClass('priority-trivial');
            }

            return $output;
        });
    }
}
