<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\pestControl\access;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DIRECTORY_TITLE = 'Access errors';
    const DIRECTORY_ICON = 'lock';
    const RECORD_ADAPTER = 'axis://pestControl/AccessLog';
    const RECORD_NAME_KEY = 'date';
    const RECORD_KEY_NAME = 'log';
    const CAN_ADD_RECORD = false;
    const CAN_EDIT_RECORD = false;

    protected $_recordListFields = [
        'date', 'mode', 'code', 'request', 'message',
        'user', 'isProduction', 'actions'
    ];

    protected $_recordDetailsFields = [
        'date', 'mode', 'code', 'request', 'userAgent',
        'message', 'user', 'isProduction'
    ];

// Record data
    protected function _describeRecord($record) {
        return $record['mode'].' '.$record['code'].' - '.$this->format->date($record['date']);
    }

    protected function _prepareRecordListQuery(opal\query\ISelectQuery $query, $mode) {
        $query
            ->importRelationBlock('user', 'link')
            ;
    }

    public function applyRecordQuerySearch(opal\query\ISelectQuery $query, $search, $mode) {
        $query->beginWhereClause()
            //->where('id', '=', ltrim($search, '#'))
            ->orWhere('code', '=', $search)
            ->orWhere('request', 'matches', $search)
            ->orWhere('message', 'matches', $search)
            ->endClause();
    }

// Fields
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

    public function defineIsProductionField($list, $mode) {
        $list->addField('isProduction', $mode == 'list' ? $this->_('Prod') : $this->_('Production'), function($log, $context) use($mode) {
            if(!$log['isProduction']) {
                $context->getRowTag()->addClass('inactive');
            }

            return $this->html->booleanIcon($log['isProduction']);
        });
    }
}