<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\system\notFound\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class ErrorList extends arch\component\template\CollectionList {

    protected $_fields = [
        'date' => true,
        'mode' => true,
        'request' => true,
        'message' => true,
        'user' => true,
        'isProduction' => true,
        'actions' => true
    ];


// Date
    public function addDateField($list) {
        $list->addField('date', function($error) {
            return $this->import->component('ErrorLink', '~admin/system/not-found/', $error);
        });
    }

// Request
    public function addRequestField($list) {
        $list->addField('request', function($error) {
            if($error['request']) {
                $name = explode('://', $error['request'])[1];
                return $this->html->link($error['request'], $this->format->shorten($name, 30))
                    ->setTitle($name);
            }
        });
    }

// Message
    public function addMessageField($list) {
        $list->addField('message', function($error) {
            return $this->format->shorten($error['message'], 40);
        });
    }

// User
    public function addUserField($list) {
        $list->addField('user', function($error) {
            return $this->import->component('UserLink', '~admin/users/clients/', $error['user'])
                ->isNullable(true)
                ->setDisposition('transitive');
        });
    }

// Production
    public function addIsProductionField($list) {
        $list->addField('isProduction', $this->_('Prod.'), function($error) {
            return $this->html->booleanIcon($error['isProduction']);
        });
    }
    

// Actions
    public function addActionsField($list) {
        $list->addField('actions', function($error) {
            return [
                // Delete
                $this->import->component('ErrorLink', '~admin/system/not-found/', $error, $this->_('Delete'))
                    ->setAction('delete')
            ];
        });
    }
}