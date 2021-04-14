<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\pestControl\misses\logs;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;
use df\flex;

use DecodeLabs\Dictum;
use DecodeLabs\Tagged as Html;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    const TITLE = '404 error logs';
    const ICON = 'log';
    const ADAPTER = 'axis://pestControl/MissLog';
    const KEY_NAME = 'log';
    const NAME_FIELD = 'date';
    const CAN_ADD = false;
    const CAN_EDIT = false;

    const LIST_FIELDS = [
        'date', 'mode', 'url', 'request',
        'referrer', 'user', 'isBot', 'isProduction'
    ];

    const DETAILS_FIELDS = [
        'date', 'url', 'referrer', 'message',
        'userAgent', 'user', 'isProduction'
    ];

    const CAN_SELECT = true;


    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query
            ->importRelationBlock('miss', 'list')
            ->importRelationBlock('user', 'link')
            ->leftJoinRelation('userAgent', 'isBot')
            ->paginate()
                ->addOrderableFields('isBot')
                ->end()
            ;
    }



    // Components
    protected function getRecordParentUriString(array $log): ?string
    {
        return '../details?miss='.$this->data->getRelationId($log, 'miss');
    }

    public function generateRecordOperativeLinks(array $log): iterable
    {
        // Archive
        yield 'archive' => $this->html->link(
                $this->getRecordUri($log, 'archive', null, true),
                $this->_('Archive '.$this->getRecordItemName())
            )
            ->setIcon('save')
            ->isDisabled($log['isArchived']);

        // Defaults
        yield from parent::generateRecordOperativeLinks($log);
    }

    public function generateIndexSectionLinks(): iterable
    {
        yield 'index' => $this->html->link('../', $this->_('URLs'), true)
            ->setIcon('brokenLink')
            ->setDisposition('informative');

        yield 'logs' => $this->html->link('./', $this->_('Logs'))
            ->setIcon('log')
            ->setDisposition('informative')
            ->isActive(true);
    }


    // Sections
    public function renderDetailsSectionBody($log)
    {
        return [
            $log['isArchived'] ?
                $this->html->flashMessage($this->_(
                    'This log has been archived and will be stored indefinitely'
                )) :
                $this->html->flashMessage($this->_(
                    'This log has not been archived and will be deleted on or around %d%',
                    ['%d%' => $this->format->date($log['date']->modifyNew('+'.$this->data->pestControl->getPurgeThreshold()))]
                ), 'warning'),

            $this->html->panelSet()
                ->addPanel([
                    Html::{'h3'}($this->_('Log')),
                    parent::renderDetailsSectionBody($log)
                ])
                ->addPanel(function () use ($log) {
                    return [
                        Html::{'h3'}([
                            $this->_('Error'), ' - ',
                            $this->apex->component('../MissLink', $log['miss'])
                        ]),
                        $this->apex->component('../MissDetails')
                            ->setRecord($log['miss'])
                    ];
                })
        ];
    }

    // Fields
    public function defineDateField($list, $mode)
    {
        if ($mode != 'details') {
            return false;
        }

        $list->addField('date', function ($log) {
            return Html::$time->dateTime($log['date']);
        });
    }
    public function defineMissField($list, $mode)
    {
        $list->addField('error', function ($log) {
            return $this->apex->component('../MissLink', $log['miss']);
        });
    }

    public function defineUserAgentField($list, $mode)
    {
        $list->addField('userAgent', function ($log) {
            if ($agent = $log['userAgent']) {
                return Html::{'code'}($agent['body']);
            }
        });
    }

    public function defineUserField($list, $mode)
    {
        $list->addField('user', function ($log) {
            return $this->apex->component('~admin/users/clients/UserLink', $log['user'])
                ->isNullable(true);
        });
    }

    public function defineReferrerField($list, $mode)
    {
        $list->addField('referrer', function ($log) use ($mode) {
            if (!$referrer = $log['referrer']) {
                return;
            }

            return $this->html->link($referrer, Html::{'samp'}($mode == 'list' ? Dictum::shorten($referrer, 35) : $referrer))
                ->setIcon('link');
        });
    }

    public function defineModeField($list, $mode)
    {
        $list->addField('mode', function ($log) {
            return Dictum::name($log['mode']);
        });
    }

    public function defineRequestField($list, $mode)
    {
        return $this->apex->scaffold('../../')->defineRequestField($list, $mode);
    }

    public function defineMessageField($list, $mode)
    {
        $list->addField('message', function ($error) use ($mode) {
            $message = $error['message'];

            if ($mode == 'list') {
                $message = Dictum::shorten($message, 25);
            }

            $output = Html::{'samp'}($message);

            if ($mode == 'list') {
                $output->setTitle($error['message']);
            }

            return $output;
        });
    }

    public function defineIsBotField($list, $mode)
    {
        $list->addField('isBot', $this->_('Bot'), function ($log, $context) {
            if ($log['isBot']) {
                $context->getRowTag()->addClass('inactive');
            }

            return $this->html->booleanIcon($log['isBot']);
        });
    }

    public function defineIsProductionField($list, $mode)
    {
        $list->addField('isProduction', $mode == 'list' ? $this->_('Prod') : $this->_('Production'), function ($log, $context) {
            if (!$log['isProduction']) {
                $context->getRowTag()->addClass('inactive');
            }

            return $this->html->booleanIcon($log['isProduction']);
        });
    }
}
