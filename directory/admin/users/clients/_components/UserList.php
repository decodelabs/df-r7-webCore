<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\clients\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class UserList extends arch\component\template\CollectionList {

    protected $_fields = [
        'fullName' => true,
        'email' => true,
        'status' => true,
        'groups' => true,
        'country' => true,
        'joinDate' => true,
        'loginDate' => true,
        'actions' => true
    ];


// Full name
    public function addFullNameField($list) {
        $list->addField('fullName', $this->_('Name'), function($client) {
            return $this->import->component('UserLink', '~admin/users/clients/', $client);
        });
    }


// Email
    public function addEmailField($list) {
        $list->addField('email', function($client) {
            return $this->html->link($this->uri->mailto($client['email']), $client['email'])
                ->setIcon('mail')
                ->setDisposition('transitive');
        });
    }


// Status
    public function addStatusField($list) {
        $list->addField('status', function($client) {
            return $this->user->client->stateIdToName($client['status']);
        });
    }


// Join date
    public function addJoinDateField($list) {
        $list->addField('joinDate', $this->_('Joined'), function($client) {
            return $this->html->date($client['joinDate']);
        });
    }


// Login date
    public function addLoginDateField($list) {
        $list->addField('loginDate', $this->_('Login'), function($client) {
            if($client['loginDate']) {
                return $this->html->timeSince($client['loginDate']);
            }
        });
    }


// Actions
    public function addActionsField($list) {
        $list->addField('actions', function($client) {
            return [
                // Edit
                $this->import->component('UserLink', '~admin/users/clients/', $client)
                    ->setAction('edit'),

                // Delete
                $this->import->component('UserLink', '~admin/users/clients/', $client)
                    ->setAction('delete')
            ];
        });
    }
}