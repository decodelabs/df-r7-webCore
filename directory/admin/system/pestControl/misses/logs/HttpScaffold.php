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
    const RECORD_NAME_FIELD = 'date';
    const CAN_ADD_RECORD = false;
    const CAN_EDIT_RECORD = false;

    protected $_recordListFields = [
        'date', 'mode', 'request', 
        'referrer', 'isBot', 'isProduction'
    ];

    protected $_recordDetailsFields = [
        'date', 'referrer', 'message',
        'userAgent', 'user', 'isProduction'
    ];


// Record data
    protected function _prepareRecordListQuery(opal\query\ISelectQuery $query, $mode) {
        $query
            ->importRelationBlock('miss', 'list')
            ->importRelationBlock('user', 'link')
            ->leftJoinRelation('userAgent', 'isBot')
            ->paginate()
                ->addOrderableFields('isBot')
                ->end()
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
        $id = $this->getRecord()['#miss'];
        return '../details?miss='.core\string\Uuid::factory($id);
    }

    public function addIndexSectionLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link('../', $this->_('URLs'))
                ->setIcon('brokenLink')
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
                    ['%d%' => $this->format->date($log['date']->modify('+'.$this->data->pestControl->getPurgeThreshold()))]
                ), 'warning'),

            $this->html->panelSet()
                ->addPanel('details', 50, [
                    $this->html('h3', $this->_('Log')),
                    parent::renderDetailsSectionBody($log)
                ])
                ->addPanel('error', 50, function() use($log) {
                    return [
                        $this->html('h3', [
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
    public function defineDateField($list, $mode) {
        if($mode != 'details') return false;

        $list->addField('date', function($log) {
            return $this->html->userDateTime($log['date']);
        });
    }
    public function defineMissField($list, $mode) {
        $list->addField('error', function($log) {
            return $this->apex->component('../MissLink', $log['miss']);
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
                ->isNullable(true);
        });
    }

    public function defineReferrerField($list, $mode) {
        $list->addField('referrer', function($log) use($mode) {
            if(!$referrer = $log['referrer']) return;

            return $this->html->link($referrer, $this->html('samp', $mode == 'list' ? $this->format->shorten($referrer, 35) : $referrer))
                ->setIcon('link');
        });
    }

    public function defineModeField($list, $mode) {
        $list->addField('mode', function($log) {
            return $this->format->name($log['mode']);
        });
    }

    public function defineRequestField($list, $mode) {
        return $this->apex->scaffold('../../')->defineRequestField($list, $mode);
    }

    public function defineMessageField($list, $mode) {
        $list->addField('message', function($error) use($mode) {
            $message = $error['message'];

            if($mode == 'list') {
                $message = $this->format->shorten($message, 25);
            }

            $output = $this->html('samp', $message);

            if($mode == 'list') {
                $output->setTitle($error['message']);
            }

            return $output;
        });
    }

    public function defineIsBotField($list, $mode) {
        $list->addField('isBot', $this->_('Bot'), function($log, $context) {
            if($log['isBot']) {
                $context->getRowTag()->addClass('inactive');
            }
            
            return $this->html->booleanIcon($log['isBot']);
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