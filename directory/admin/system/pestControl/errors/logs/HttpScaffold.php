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
    const RECORD_NAME_FIELD = 'date';
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
            ->importRelationBlock('user', 'link')
            ;
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
        return '../details?error='.core\string\Uuid::factory($id);
    }

    public function addIndexSectionLinks($menu, $bar) {
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
                            $this->apex->component('../ErrorLink', $log['error'], $this->_('Error'))
                                ->setDisposition('transitive')
                        ]),
                        $this->apex->component('../ErrorDetails')
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
            return $this->apex->component('../ErrorLink', $log['error'])
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
            return $this->apex->component('~admin/users/clients/UserLink', $log['user'])
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
            $output = $this->uri->directoryRequest($request);

            if($mode == 'list') {
                unset($output->query->rf, $output->query->rt);
                $output = $this->format->shorten($output->toReadableString(), 60, true);
            }

            $output = $this->html('code', $output);

            if($mode == 'list') {
                $output->setTitle($request);
            }

            if($log['mode'] == 'Http') {
                $output = $this->html->link($request, $output)
                    ->setIcon('link')
                    ->setDisposition('transitive')
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