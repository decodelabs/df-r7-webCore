<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\deactivations\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class DeactivationList extends arch\component\template\CollectionList {

    protected $_fields = [
        'date' => true,
        'user' => true,
        'reason' => true,
        'actions' => true
    ];


// Date
    public function addDateField($list) {
        $list->addField('date', function($deactivation) {
            return $this->import->component('DeactivationLink', '~admin/users/deactivations/', $deactivation);
        });
    }

// User
    public function addUserField($list) {
        $list->addField('user', function($deactivation) {
            return $this->import->component('UserLink', '~admin/users/clients/', $deactivation['user'])
                ->setDisposition('transitive');
        });
    }


// Actions
    public function addActionsField($list) {
        $list->addField('actions', function($deactivation) {
            return [
                // Delete
                $this->import->component('DeactivationLink', '~admin/users/deactivations/', $deactivation)
                    ->setAction('delete')
            ];
        });
    }
}