<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\pestControl\errors\logs;

use DecodeLabs\Dictum;

use DecodeLabs\Tagged as Html;
use df\arch;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const TITLE = 'Critical error logs';
    public const ICON = 'log';
    public const ADAPTER = 'axis://pestControl/ErrorLog';
    public const KEY_NAME = 'log';
    public const NAME_FIELD = 'date';
    public const CAN_ADD = false;
    public const CAN_EDIT = false;

    public const LIST_FIELDS = [
        'date', 'mode', 'request', 'message',
        'user', 'isProduction'
    ];

    public const DETAILS_FIELDS = [
        'date', 'mode', 'request', 'referrer', 'message',
        'userAgent', 'user', 'isProduction'
    ];

    public const CAN_SELECT = true;

    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query
            ->importRelationBlock('error', 'list')
            ->importRelationBlock('user', 'link')
        ;
    }



    // Components
    protected function getRecordParentUriString(array $log): ?string
    {
        return '../details?error=' . $this->data->getRelationId($log, 'error');
    }

    public function generateRecordOperativeLinks(array $log): iterable
    {
        // Archive
        yield 'archive' => $this->html->link(
            $this->getRecordUri($log, 'archive', null, true),
            $this->_('Archive ' . $this->getRecordItemName())
        )
            ->setIcon('save')
            ->isDisabled($log['isArchived']);

        // Defaults
        yield from parent::generateRecordOperativeLinks($log);
    }

    public function generateIndexSectionLinks(): iterable
    {
        yield 'index' => $this->html->link('../', $this->_('Errors'), true)
            ->setIcon('error')
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
                    ['%d%' => Dictum::$time->date($log['date']->modifyNew('+' . $this->data->pestControl->getPurgeThreshold()))]
                ), 'warning'),

            $this->html->panelSet()
                ->addPanel([
                    Html::{'h3'}($this->_('Log')),
                    parent::renderDetailsSectionBody($log)
                ])
                ->addPanel(function () use ($log) {
                    return [
                        Html::{'h3'}([
                            $this->apex->component('../ErrorLink', $log['error'], $this->_('Error'))
                        ]),
                        $this->apex->component('../ErrorDetails')
                            ->setRecord($log['error'])
                    ];
                }),

            Html::{'h3'}($this->_('Stack trace')),

            function () use ($log) {
                if (!$trace = $log['stackTrace']) {
                    return $this->html->flashMessage($this->_(
                        'No stack trace was stored with this error log'
                    ), 'error');
                }

                $trace = json_decode($trace['body'], true);

                return $this->html->collectionList($trace)
                    ->addField('file', function ($call) {
                        if ($call['file']) {
                            return Html::{'code'}($call['file'] . ' : ' . $call['line']);
                        }
                    })
                    ->addField('signature', function ($call) {
                        return Html::{'code'}($call['signature']);
                    });
            }
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
    public function defineErrorField($list, $mode)
    {
        $list->addField('error', function ($log) {
            return $this->apex->component('../ErrorLink', $log['error']);
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

    public function defineMessageField($list, $mode)
    {
        if ($mode == 'list') {
            $list->addField('message', function ($log) {
                $message = $log['message'];

                if ($message === null) {
                    $message = $log['origMessage'];
                }

                $message = Dictum::shorten($message, 25);

                return Html::{'samp'}($message, [
                    'title' => $log['message']
                ]);
            });
        } else {
            $list->addField('message', function ($log, $context) {
                $message = $log['message'];

                if ($message === null) {
                    $context->skipRow();
                    return;
                }

                return Html::{'samp'}($message);
            });
        }
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
