<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\errorLogs\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class LogList extends arch\component\template\CollectionList {

    protected $_fields = [
        'date' => true,
        'code' => true,
        'mode' => true,
        'request' => true,
        'message' => true,
        'user' => true,
        'isProduction' => true,
        'actions' => true
    ];


// Date
    public function addDateField($list) {
        $list->addField('date', function($log) {
            return $this->import->component('LogLink', '~admin/system/error-logs/', $log);
        });
    }


// Code
    public function addCodeField($list) {
        $list->addField('code', function($log) {
            $icon = 'info';

            if($log['code'] == 404) {
                $icon = 'warning';
            } else if($log['code'] == 500) {
                $icon = 'error';
            }

            return $this->html->icon($icon, $log['code'])
                ->addClass('state-'.$icon);
        });
    }

// Request
    public function addRequestField($list) {
        $list->addField('request', function($log) {
            if($log['request']) {
                return $this->html->link($log['request'], explode('://', $log['request'])[1]);
            }
        });
    }

// Message
    public function addMessageField($list) {
        $list->addField('message', function($log) {
            return $this->format->shorten($log['message'], 40);
        });
    }

// User
    public function addUserField($list) {
        $list->addField('user', function($log) {
            return $this->import->component('UserLink', '~admin/users/', $log['user'])
                ->isNullable(false)
                ->setDisposition('transitive');
        });
    }

// Production
    public function addIsProductionField($list) {
        $list->addField('isProduction', $this->_('Prod.'), function($log) {
            return $this->html->booleanIcon($log['isProduction']);
        });
    }
    

// Actions
    public function addActionsField($list) {
        $list->addField('actions', function($log) {
            return [
                // Delete
                $this->import->component('LogLink', '~admin/system/error-logs/', $log, $this->_('Delete'))
                    ->setAction('delete')
            ];
        });
    }
}