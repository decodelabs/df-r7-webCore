<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\_components;

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
            return $this->view->import->component('UserLink', '~admin/users/', $client);
        });
    }


// Email
    public function addEmailField($list) {
        $list->addField('email', function($client) {
            return $this->html->link($this->view->uri->mailto($client['email']), $client['email'])
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
                $this->view->import->component('UserLink', '~admin/users/', $client)
                    ->setAction('edit')
                    ->addAccessLock('axis://user/Client#edit'),

                $this->view->import->component('UserLink', '~admin/users/', $client)
                    ->setAction('delete')
                    ->addAccessLock('axis://user/Client#delete')
            ];
        });
    }
}