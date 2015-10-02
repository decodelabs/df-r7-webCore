<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\hooks;

use df;
use df\core;
use df\apex;
use df\spur;
use df\flow;
use df\mesh;
use df\flex;

class EmailVerify extends mesh\event\Hook {

    protected $_events = [
        'axis://user/Client' => [
            'insert' => 'clientInsert',
            'preUpdate' => 'clientUpdate',
            'preDelete' => 'clientDelete'
        ]
    ];

    public function onClientInsert($event) {
        $record = $event->getCachedEntity();
        $this->_verify($event, $record);
    }

    public function onClientUpdate($event) {
        $record = $event->getCachedEntity();

        if($record->hasChanged('email')) {
            $this->_verify($event, $record);
        }
    }

    protected function _verify($event, $record) {
        if(!$this->data->user->emailVerify->isVerified($record['id'], $record['email'])) {
            $taskSet = $event['taskSet'];
            $task = $event['task'];

            $key = $this->data->user->emailVerify->select('key')
                ->where('user', '=', $record['id'])
                ->where('email', '=', $record['email'])
                ->toValue('key');

            if(!$key) {
                $key = flex\Generator::random(12, 16);
            }

            $emailTask = $taskSet->addRawQuery('verifyEmail',
                $this->data->user->emailVerify->insert([
                        'user' => $record,
                        'email' => $record['email'],
                        'key' => $key
                    ])
                    ->ifNotExists(true)
            );

            $emailTask->addDependency($task);

            if($this->data->user->config->shouldVerifyEmail()) {
                $this->context->comms->componentNotify(
                    'account/EmailVerify',
                    [$key, $record]
                );
            }
        }
    }

    public function onClientDelete($event) {
        $this->data->user->emailVerify->delete()
            ->where('user', '=', $event->getCachedEntity())
            ->execute();
    }
}