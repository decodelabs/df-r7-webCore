<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\pestControl\errors\logs;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;
use df\flex;

use DecodeLabs\Tagged\Html;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    const TITLE = 'Critical error logs';
    const ICON = 'log';
    const ADAPTER = 'axis://pestControl/ErrorLog';
    const KEY_NAME = 'log';
    const NAME_FIELD = 'date';
    const CAN_ADD = false;
    const CAN_EDIT = false;

    const LIST_FIELDS = [
        'date', 'mode', 'request', 'message',
        'user', 'isProduction'
    ];

    const DETAILS_FIELDS = [
        'date', 'mode', 'request', 'referrer', 'message',
        'userAgent', 'user', 'isProduction'
    ];

    const CAN_SELECT = true;

    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query
            ->importRelationBlock('error', 'list')
            ->importRelationBlock('user', 'link')
            ;
    }

    public function getRecordOperativeLinks($record, $mode)
    {
        return array_merge(
            [
                $this->html->link(
                        $this->_getRecordNodeRequest($record, 'archive', null, true),
                        $this->_('Archive '.$this->getRecordItemName())
                    )
                    ->setIcon('save')
                    ->isDisabled($record['isArchived'])
            ],
            parent::getRecordOperativeLinks($record, $mode)
        );
    }


    // Components
    protected function getParentSectionRequest()
    {
        $id = $this->getRecord()['#error'];
        return '../details?error='.flex\Guid::factory($id);
    }

    public function addIndexSectionLinks($menu, $bar)
    {
        $menu->addLinks(
            $this->html->link('../', $this->_('Errors'))
                ->setIcon('error')
                ->setDisposition('informative'),

            $this->html->link('./', $this->_('Logs'))
                ->setIcon('log')
                ->setDisposition('informative')
                ->isActive(true)
        );
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
                            return Html::{'code'}($call['file'].' : '.$call['line']);
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
            return $this->format->name($log['mode']);
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

            return $this->html->link($referrer, Html::{'samp'}($mode == 'list' ? $this->format->shorten($referrer, 35) : $referrer))
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

                $message = $this->format->shorten($message, 25);

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
