<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\pestControl\misses;

use DecodeLabs\Dictum;

use DecodeLabs\Tagged as Html;
use df\arch;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const TITLE = '404 errors';
    public const ICON = 'brokenLink';
    public const ADAPTER = 'axis://pestControl/Miss';
    public const ITEM_NAME = '404 error';
    public const NAME_FIELD = 'id';
    public const CAN_ADD = false;
    public const CAN_EDIT = false;

    public const LIST_FIELDS = [
        'id', 'mode', 'request',
        'seen', 'lastSeen', 'bots'
    ];

    public const CAN_SELECT = true;


    // Sections
    public function renderDetailsSectionBody($miss)
    {
        return [
            parent::renderDetailsSectionBody($miss),

            $this->apex->scaffold('./logs/')
                ->renderRecordList(function ($query) use ($miss) {
                    $query->where('miss', '=', $miss['id']);
                })
        ];
    }


    // Components
    public function generateRecordOperativeLinks(array $miss): iterable
    {
        // Archive
        yield 'archive' => $this->html->link(
            $this->getRecordUri($miss, 'archive', null, true),
            $this->_('Archive ' . $this->getRecordItemName())
        )
            ->setIcon('remove')
            ->isDisabled(isset($miss['archiveDate']));

        // Defaults
        yield from parent::generateRecordOperativeLinks($miss);
    }

    public function generateIndexSectionLinks(): iterable
    {
        yield 'index' => $this->html->link('./', $this->_('URLs'))
            ->setIcon('brokenLink')
            ->setDisposition('informative')
            ->isActive(true);

        yield 'logs' => $this->html->link('./logs/', $this->_('Logs'))
            ->setIcon('log')
            ->setDisposition('informative');
    }

    public function generateIndexOperativeLinks(): iterable
    {
        yield 'purge' => $this->html->link($this->uri('./purge', true), $this->_('Purge old logs'))
            ->setIcon('delete');

        yield 'purgeAll' => $this->html->link($this->uri('./purge-all', true), $this->_('Purge ALL'))
            ->setIcon('delete');
    }


    // Fields
    public function defineModeField($list, $mode)
    {
        $list->addField('mode', function ($miss) {
            return Dictum::name($miss['mode']);
        });
    }

    public function defineRequestField($list, $mode)
    {
        return $this->apex->scaffold('../')->defineRequestField($list, $mode);
    }

    public function defineSeenField($list, $mode)
    {
        $list->addField('seen', function ($miss) {
            $output = Html::{'span'}($this->_(
                [
                    'n == 1' => '%n% time',
                    '*' => '%n% times'
                ],
                ['%n%' => $miss['seen']],
                $miss['seen']
            ));

            if ($miss['seen'] > 100) {
                $output->addClass('priority-critical');
            } elseif ($miss['seen'] > 50) {
                $output->addClass('priority-high');
            } elseif ($miss['seen'] > 20) {
                $output->addClass('priority-medium');
            } elseif ($miss['seen'] > 5) {
                $output->addClass('priority-low');
            } else {
                $output->addClass('priority-trivial');
            }

            return $output;
        });
    }

    public function defineLastSeenField($list, $mode)
    {
        $list->addField('lastSeen', function ($miss, $context) use ($mode) {
            if ($mode == 'list' && $miss['archiveDate']) {
                $context->getRowTag()->addClass('disabled');
            }

            $output = Html::$time->since($miss['lastSeen']);

            if ($miss['lastSeen']->gt('-1 day')) {
                $output->addClass('priority-critical');
            } elseif ($miss['lastSeen']->gt('-3 days')) {
                $output->addClass('priority-high');
            } elseif ($miss['lastSeen']->gt('-1 week')) {
                $output->addClass('priority-medium');
            } elseif ($miss['lastSeen']->gt('-2 weeks')) {
                $output->addClass('priority-low');
            } else {
                $output->addClass('priority-trivial');
            }

            return $output;
        });
    }

    public function defineBotsField($list, $mode)
    {
        $list->addField('botsSeen', $this->_('Bots'), function ($miss) {
            $percent = (100 / $miss['seen']) * $miss['botsSeen'];
            $output = Html::$number->percent($percent);

            if ($percent > 0) {
                $output = $this->html->icon('warning', $output);

                if ($percent >= 50) {
                    $output->addClass('error');
                } else {
                    $output->addClass('warning');
                }
            }

            return $output;
        });
    }
}
