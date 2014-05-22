<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\accessErrors;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DIRECTORY_TITLE = 'Access error logs';
    const DIRECTORY_ICON = 'lock';
    const RECORD_ADAPTER = 'axis://log/AccessError';
    const RECORD_NAME_KEY = 'date';
    const RECORD_KEY_NAME = 'error';

    const CAN_ADD_RECORD = false;
    const CAN_EDIT_RECORD = false;

    protected $_recordListFields = [
        'date' => true,
        'mode' => true,
        'code' => true,
        'request' => true,
        'message' => true,
        'user' => true,
        'isProduction' => true,
        'actions' => true
    ];

    protected $_recordDetailsFields = [
        'date' => true,
        'user' => true,
        'mode' => true,
        'code' => true,
        'request' => true,
        'query' => true,
        'userAgent' => true,
        'message' => true
    ];


// Record data
    protected function _prepareRecordListQuery(opal\query\ISelectQuery $query, $mode) {
        $query->importRelationBlock('user', 'link');
    }

    protected function _describeRecord($record) {
        return $record['mode'].' '.$record['code'].' - '.$this->format->date($record['date']);
    }


// Components
    public function addIndexHeaderBarOperativeLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~admin/system/access-errors/delete-all', true),
                    $this->_('Delete all errors')
                )
                ->setIcon('delete')
        );
    }


// Fields
    public function defineModeField($list, $mode) {
        $list->addField('mode', function($error) use($mode) {
            $output = $error['mode'];

            if($mode != 'list') {
                $output = [
                    $output, ' ',
                    $this->html->element('sup', '('.($error['isProduction'] ? $this->_('production') : $this->_('testing')).')')
                        ->addClass($error['isProduction'] ? 'state-error' : 'state-warning')
                ];
            }

            return $output;
        });
    }

    public function defineCodeField($list, $mode) {
        $list->addField('code', function($error) use($mode) {
            $output = $error['code'];

            if($mode != 'list') {
                $output = $this->http->statusCodeToString($output);
            }

            return $output;
        });
    }

    public function defineRequestField($list, $mode) {
        $list->addField('request', function($error) use($mode) {
            if(!$error['request']) {
                return;
            }

            $request = $this->directory->newRequest($error['request']);
            $name = $request->path->toString();

            return $this->html->link($error['request'], $name)
                ->setTitle($name);
        });
    }

    public function defineQueryField($list) {
        $list->addField('queryData', function($error) {
            if(!$error['request']) {
                return;
            }
         
            $query = $this->directory->newRequest($error['request'])->query;

            if($query->isEmpty()) {
                return;
            }

            return $this->_defineQueryNode($query);
        });
    }

    protected function _defineQueryNode($node) {
        $list = $this->html->attributeList($node);

        foreach($node->getKeys() as $key) {
            $list->addField($key, $key, function($node) use($key) {
                $node = $node->{$key};
                $output = [];

                if($node->hasValue()) {
                    $value = $node->getValue();

                    if($key == 'rf' || $key == 'rt') {
                        $value = arch\Request::decode($value);
                    }

                    $output[] = $value;
                }

                if(count($node)) {
                    $output[] = $this->_defineQueryNode($node);
                }

                return $output;
            });
        }

        return $list;
    }

    public function defineMessageField($list, $mode) {
        $list->addField('message', function($error) use($mode) {
            $output = $error['message'];

            if($mode == 'list') {
                $output = $this->format->shorten($output, 40);
            }

            return $this->html->element('code', $output);
        });
    }

    public function defineUserField($list) {
        $list->addField('user', function($error) {
            return $this->import->component('UserLink', '~admin/users/clients/', $error['user'])
                ->isNullable(true)
                ->setDisposition('transitive');
        });
    }

    public function defineIsProductionField($list) {
        $list->addField('isProduction', $this->_('Prod.'), function($error) {
            return $this->html->booleanIcon($error['isProduction']);
        });
    }

    public function defineUserAgentField($list) {
        $list->addField('userAgent', function($error) {
            return $this->html->element('code', $error['userAgent']);
        });
    }
}