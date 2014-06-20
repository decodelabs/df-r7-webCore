<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\inviteRequests\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\flex;

class HttpExport extends arch\Action {
    
    public function execute() {
        return $this->http->csvGenerator('Invite requests ('.$this->format->date('now').').csv', function($builder) {
            $q = $this->data->user->inviteRequest->select()
                ->leftJoinRelation('invite', 'registrationDate', 'user')
                ->orderBy('creationDate DESC');

            $builder->setFields([
                'creationDate' => 'Date',
                'name' => 'Name',
                'email' => 'Email',
                'companyName' => 'Company',
                'companyPosition' => 'Position',
                'status' => 'Status',
                'registrationDate' => 'Registered',
                'user' => 'User ID',
                'message' => 'Message'
            ]);

            foreach($q as $row) {
                $row['creationDate'] = $this->format->dateTime($row['creationDate']);
                $row['registrationDate'] = $this->format->dateTime($row['registrationDate']);

                if($row['invite']) {
                    $row['status'] = 'Accepted';
                } else if(!$row['isActive']) {
                    $row['status'] = 'Declined';
                } else {
                    $row['status'] = 'Pending';
                }

                if($row['user']) {
                    $row['user'] = (string)$row['user'];
                }

                $builder->addRow($row);
            }
        });
    }
}