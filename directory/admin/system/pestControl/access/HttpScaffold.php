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

class HttpScaffold extends arch\scaffold\RecordAdmin {

    const DIRECTORY_TITLE = 'Access errors';
    const DIRECTORY_ICON = 'lock';
    const RECORD_ADAPTER = 'axis://pestControl/AccessLog';
    const RECORD_NAME_FIELD = 'date';
    const RECORD_KEY_NAME = 'log';
    const CAN_ADD_RECORD = false;
    const CAN_EDIT_RECORD = false;

    protected $_recordListFields = [
        'date', 'mode', 'code', 'request', 'message',
        'user', 'isProduction'
    ];

    protected $_recordDetailsFields = [
        'date', 'mode', 'code', 'request', 'userAgent',
        'message', 'user', 'isProduction'
    ];

// Record data
    protected function describeRecord($record) {
        return $record['mode'].' '.$record['code'].' - '.$this->format->date($record['date']);
    }

    protected function prepareRecordList($query, $mode) {
        $query
            ->importRelationBlock('user', 'link')
            ;
    }

// Components
    public function addIndexOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link($this->uri('./purge', true), $this->_('Purge old logs'))
                ->setIcon('delete')
        );
    }

// Fields
    public function defineModeField($list, $mode) {
        $list->addField('mode', function($log) {
            return $this->format->name($log['mode']);
        });
    }

    public function defineRequestField($list, $mode) {
        return $this->apex->scaffold('../')->defineRequestField($list, $mode);
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

    public function defineIsProductionField($list, $mode) {
        $list->addField('isProduction', $mode == 'list' ? $this->_('Prod') : $this->_('Production'), function($log, $context) use($mode) {
            if(!$log['isProduction']) {
                $context->getRowTag()->addClass('inactive');
            }

            return $this->html->booleanIcon($log['isProduction']);
        });
    }
}