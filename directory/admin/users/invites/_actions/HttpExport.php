<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\invites\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\flex;

class HttpExport extends arch\action\Base {

    public function execute() {
        return $this->http->csvGenerator('Invites ('.$this->format->date('now').').csv', function($builder) {
            $q = $this->data->user->invite->select()
                ->leftJoinRelation('owner', 'fullName as owner')
                ->leftJoinRelation('user', 'fullName as userName')
                ->populateSelect('groups', 'name')
                ->orderBy('lastSent DESC');

            $builder->setFields([
                'creationDate' => 'Created',
                'email' => 'Email',
                'name' => 'Name',
                'lastSent' => 'Last sent',
                'owner' => 'Sent by',
                'status' => 'Status',
                'registrationDate' => 'Registered',
                'groups' => 'Groups',
                'message' => 'Message'
            ]);

            foreach($q as $row) {
                $row['creationDate'] = $this->format->dateTime($row['creationDate']);
                $row['registrationDate'] = $this->format->dateTime($row['registrationDate']);
                $row['lastSent'] = $this->format->dateTime($row['lastSent']);
                $groups = [];

                foreach($row['groups'] as $group) {
                    $groups[] = $group['name'];
                }

                $row['groups'] = implode(', ', $groups);

                if($row['userName']) {
                    $row['name'] = $row['userName'];
                }

                if($row['user']) {
                    $row['status'] = 'Accepted';
                } else if($row['isActive']) {
                    $row['status'] = 'Pending';
                } else {
                    $row['status'] = 'Cancelled';
                }

                $builder->addRow($row);
            }
        });
    }
}