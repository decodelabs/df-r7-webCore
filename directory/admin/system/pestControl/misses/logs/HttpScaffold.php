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

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DIRECTORY_TITLE = '404 error logs';
    const DIRECTORY_ICON = 'log';
    const RECORD_ADAPTER = 'axis://pestControl/MissLog';
    const RECORD_KEY_NAME = 'log';
    const RECORD_NAME_KEY = 'date';
    const CAN_ADD_RECORD = false;
    const CAN_EDIT_RECORD = false;

    protected $_recordListFields = [
        'date', 'mode', 'request', 'message', 
        'user', 'isProduction', 'actions'
    ];

    protected $_recordDetailsFields = [
        'date', 'referrer',
        'userAgent', 'user', 'isProduction'
    ];


// Record data
    protected function _prepareRecordListQuery(opal\query\ISelectQuery $query, $mode) {
        $query
            ->importRelationBlock('miss', 'list')
            ->importRelationBlock('user', 'link')
            ;
    }

    public function applyRecordQuerySearch(opal\query\ISelectQuery $query, $search, $mode) {
        $query->beginWhereClause()
            ->where('id', '=', ltrim($search, '#'))
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
        $id = $this->getRecord()->getRawId('miss');
        return '~admin/system/pestControl/misses/logs?miss='.$id;
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
                    $this->html->element('h3', $this->_('Log')),
                    parent::renderDetailsSectionBody($log)
                ])
                ->addPanel('error', 50, function() use($log) {
                    return [
                        $this->html->element('h3', [
                            $this->_('Error'), ' - ',
                            $this->import->component('~admin/system/pestControl/misses/MissLink', $log['miss'])
                                ->setDisposition('transitive')
                        ]),
                        $this->import->component('~admin/system/pestControl/misses/MissDetails')
                            ->setRecord($log['miss'])
                    ];
                })
        ];
    }

// Fields
    public function defineDateField($list, $mode) {
        if($mode != 'details') return false;

        $list->addField('date', function($log) {
            return $this->html->userDateTime($log['date']);
        });
    }
    public function defineMissField($list, $mode) {
        $list->addField('error', function($log) {
            return $this->import->component('~admin/system/pestControl/misses/MissLink', $log['miss'])
                ->setDisposition('transitive');
        });
    }

    public function defineUserAgentField($list, $mode) {
        $list->addField('userAgent', function($log) {
            if($agent = $log['userAgent']) {
                return $this->html->element('code', $agent['body']);
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

    public function defineReferrerField($list, $mode) {
        $list->addField('referrer', function($log) {
            if(!$referrer = $log['referrer']) return;

            return $this->html->link($referrer, $this->html->element('samp', $referrer))
                ->setIcon('link');
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
                $output = $this->normalizeOutputUrl($output);

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

            $output = $this->html->element('code', $output);

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
        $list->addField('message', function($error) use($mode) {
            $message = $error['message'];

            if($mode == 'list') {
                $message = $this->format->shorten($message, 25);
            }

            $output = $this->html->element('samp', $message);

            if($mode == 'list') {
                $output->setAttribute('title', $error['message']);
            }

            return $output;
        });
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