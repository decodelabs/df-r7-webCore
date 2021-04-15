<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\users\inviteRequests\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;
use df\flex;

use DecodeLabs\Dictum;

class HttpExport extends arch\node\Base
{
    public function execute()
    {
        return $this->http->csvGenerator('Invite requests ('.Dictum::$time->date('now').').csv', function ($builder) {
            $q = $this->data->user->inviteRequest->select()
                ->leftJoinRelation('invite', 'registrationDate', 'user as inviteUser')
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

            foreach ($q as $row) {
                $row['creationDate'] = Dictum::$time->dateTime($row['creationDate']);
                $row['registrationDate'] = Dictum::$time->dateTime($row['registrationDate']);

                if ($row['invite']) {
                    $row['status'] = 'Accepted';
                } elseif (!$row['isActive']) {
                    $row['status'] = 'Declined';
                } else {
                    $row['status'] = 'Pending';
                }

                if ($row['inviteUser']) {
                    $row['user'] = $row['inviteUser'];
                }

                if ($row['user']) {
                    $row['user'] = (string)$row['user'];
                }

                $builder->addRow($row);
            }
        });
    }
}
