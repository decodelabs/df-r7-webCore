<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\invites\_nodes;

use DecodeLabs\Dictum;

use DecodeLabs\R7\Legacy;
use df\arch;

class HttpExport extends arch\node\Base
{
    public function execute()
    {
        return Legacy::$http->csvGenerator('Invites (' . Dictum::$time->date('now') . ').csv', function ($builder) {
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

            foreach ($q as $row) {
                $row['creationDate'] = Dictum::$time->dateTime($row['creationDate']);
                $row['registrationDate'] = Dictum::$time->dateTime($row['registrationDate']);
                $row['lastSent'] = Dictum::$time->dateTime($row['lastSent']);
                $groups = [];

                foreach ($row['groups'] as $group) {
                    $groups[] = $group['name'];
                }

                $row['groups'] = implode(', ', $groups);

                if ($row['userName']) {
                    $row['name'] = $row['userName'];
                }

                if ($row['user']) {
                    $row['status'] = 'Accepted';
                } elseif ($row['isActive']) {
                    $row['status'] = 'Pending';
                } else {
                    $row['status'] = 'Cancelled';
                }

                $builder->addRow($row);
            }
        });
    }
}
