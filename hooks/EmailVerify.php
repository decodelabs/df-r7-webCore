<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\hooks;

use DecodeLabs\R7\Config\Users as UserConfig;
use df\flex;
use df\mesh;

class EmailVerify extends mesh\event\Hook
{
    public const EVENTS = [
        'axis://user/Client' => [
            'insert' => 'clientInsert',
            'preUpdate' => 'clientUpdate',
            'preDelete' => 'clientDelete'
        ]
    ];

    public function onClientInsert($event)
    {
        $record = $event->getCachedEntity();
        $this->_verify($event, $record);
    }

    public function onClientUpdate($event)
    {
        $record = $event->getCachedEntity();

        if ($record->hasChanged('email')) {
            $this->_verify($event, $record);
        }
    }

    protected function _verify($event, $record)
    {
        if (!$this->data->user->emailVerify->isVerified($record['id'], $record['email'])) {
            $queue = $event->getJobQueue();

            $key = $this->data->user->emailVerify->select('key')
                ->where('user', '=', $record['id'])
                ->where('email', '=', $record['email'])
                ->toValue('key');

            if (!$key) {
                $key = flex\Generator::random(12, 16);
            }

            $queue->after(
                $event->getJob(),
                'verifyEmail',
                $this->data->user->emailVerify->insert([
                        'user' => $record,
                        'email' => $record['email'],
                        'key' => $key
                    ])
                    ->ifNotExists(true)
            );

            if (UserConfig::load()->shouldVerifyEmail()) {
                $this->comms->sendPreparedMail('account/EmailVerify', [
                    'user' => $record,
                    'key' => $key
                ]);
            }
        }
    }

    public function onClientDelete($event)
    {
        $this->data->user->emailVerify->delete()
            ->where('user', '=', $event->getCachedEntity())
            ->execute();
    }
}
