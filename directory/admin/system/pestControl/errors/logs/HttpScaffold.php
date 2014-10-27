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

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DIRECTORY_TITLE = 'Critical error logs';
    const DIRECTORY_ICON = 'log';
    const RECORD_ADAPTER = 'axis://pestControl/ErrorLog';
    const RECORD_KEY_NAME = 'log';
    const RECORD_NAME_KEY = 'date';
    const CAN_ADD_RECORD = false;
    const CAN_EDIT_RECORD = false;

    protected $_recordListFields = [
        'date', 'mode', 'request', 'message', 
        'user', 'isProduction', 'actions'
    ];

    protected $_recordDetailsFields = [
        'date', 'mode', 'request', 'message',
        'userAgent', 'user', 'isProduction'
    ];

// Record data
    protected function _prepareRecordListQuery(opal\query\ISelectQuery $query, $mode) {
        $query
            ->importRelationBlock('error', 'list')
            ;
    }

    public function applyRecordQuerySearch(opal\query\ISelectQuery $query, $search, $mode) {
        $query->beginWhereClause()
            //->where('id', '=', ltrim($search, '#'))
            ->orWhere('request', 'matches', $search)
            ->orWhere('message', 'matches', $search)
            ->endClause();
    }

    public function getRecordOperativeLinks($record, $mode) {
        return array_merge(
            [
                $this->html->link(
                        $this->_getRecordActionRequest($record, 'archive', null, true),
                        $this->_('Archive '.$this->getRecordItemName())
                    )
                    ->setIcon('save')
                    ->isDisabled($record['isArchived'])
            ],
            parent::getRecordOperativeLinks($record, $mode)
        );
    }


// Components
    protected function _getSectionHeaderBarBackLinkRequest() {
        $id = $this->getRecord()->getRawId('error');
        return '~admin/system/pestControl/errors/details?error='.core\string\Uuid::factory($id);
    }

// Sections
    public function renderDetailsSectionBody($log) {
        return [
            $log['isArchived'] ?
                $this->html->flashMessage($this->_(
                    'This log has been archived and will be stored indefinitely'
                )) :
                $this->html->flashMessage($this->_(
                    'This log has not been archived and will be deleted on or around %d%',
                    ['%d%' => $this->format->date($log['date']->modify('+3 months'))]
                ), 'warning'),

            $this->html->panelSet()
                ->addPanel('details', 50, [
                    $this->html('h3', $this->_('Log')),
                    parent::renderDetailsSectionBody($log)
                ])
                ->addPanel('error', 50, function() use($log) {
                    return [
                        $this->html('h3', [
                            $this->import->component('~admin/system/pestControl/errors/ErrorLink', $log['error'], $this->_('Error'))
                                ->setDisposition('transitive')
                        ]),
                        $this->import->component('~admin/system/pestControl/errors/ErrorDetails')
                            ->setRecord($log['error'])
                    ];
                }),

            $this->html('h3', $this->_('Stack trace')),

            function() use($log) {
                if(!$trace = $log['stackTrace']) {
                    return $this->html->flashMessage($this->_(
                        'No stack trace was stored with this error log'
                    ), 'error');
                }

                $trace = json_decode($trace['body'], true);

                return $this->html->collectionList($trace)
                    ->addField('file', function($call) {
                        if($call['file']) {
                            return $this->html('code', $call['file'].' : '.$call['line']);
                        }
                    })
                    ->addField('signature', function($call) {
                        return $this->html('code', $call['signature']);
                    });
            }
        ];
    }

// Fields
    public function defineDateField($list, $mode) {
        if($mode != 'details') return false;

        $list->addField('date', function($log) {
            return $this->html->userDateTime($log['date']);
        });
    }
    public function defineErrorField($list, $mode) {
        $list->addField('error', function($log) {
            return $this->import->component('~admin/system/pestControl/errors/ErrorLink', $log['error'])
                ->setDisposition('transitive');
        });
    }

    public function defineUserAgentField($list, $mode) {
        $list->addField('userAgent', function($log) {
            if($agent = $log['userAgent']) {
                return $this->html('code', $agent['body']);
            }
        });
    }

    public function defineUserField($list, $mode) {
        $list->addField('user', function($log) {
            return $this->import->component('~admin/users/clients/UserLink', $log['user'])
                ->setDisposition('transitive')
                ->isNullable(true);
        });
    }

    public function defineModeField($list, $mode) {
        $list->addField('mode', function($log) {
            return $this->format->name($log['mode']);
        });
    }

    public function defineRequestField($list, $mode) {
        $list->addField('request', function($log, $context) use($mode) {
            if(!$request = $log['request']) return;
            $context->getCellTag()->setStyle('word-break', 'break-all');

            $output = $request;
            $link = false;

            if(substr($request, 0, 4) == 'http') {
                $output = $this->uri($output);

                if($mode == 'list') {
                    $output = $this->format->shorten((string)$output->getPath(), 35, true);
                }
            } else if(substr($request, 0, 9) == 'directory') {
                $output = $this->directory->newRequest($request);

                if($mode == 'list') {
                    $output = $this->format->shorten((string)$output->getPath(), 35, true);
                }
            } else if($mode == 'list') {
                $output = $this->format->shorten($output, 35, true);
            }

            $output = $this->html('code', $output);

            if($mode == 'list') {
                $output->setAttribute('title', $request);
            }

            if($link) {
                $output = $this->html->link($request, $output)
                    ->setIcon('link')
                    ->setTarget('_blank');
            }

            return $output;
        });
    }

    public function defineMessageField($list, $mode) {
        if($mode == 'list') {
            $list->addField('message', function($log) {
                $message = $log['message'];

                if($message === null) {
                    $message = $log['origMessage'];
                }

                $message = $this->format->shorten($message, 25);

                return $this->html('samp', $message, [
                    'title' => $log['message']
                ]);
            });
        } else {
            $list->addField('message', function($log, $context) {
                $message = $log['message'];

                if($message === null) {
                    $context->skipRow();
                    return;
                }

                return $this->html('samp', $message);
            });
        }
    }

    public function defineIsProductionField($list, $mode) {
        $list->addField('isProduction', $mode == 'list' ? $this->_('Prod') : $this->_('Production'), function($log, $context) use($mode) {
            if(!$log['isProduction']) {
                $context->getRowTag()->addClass('inactive');
            }

            return $this->html->booleanIcon($log['isProduction']);
        });
    }
}