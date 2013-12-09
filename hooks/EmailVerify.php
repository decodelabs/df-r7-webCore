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
    
class EmailVerify extends core\policy\Hook {

    protected static $_actionMap = [
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
                $key = core\string\Generator::random(12, 16);
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
                $this->context->comms->templateNotify(
                    'emails/EmailVerify.html',
                    '~front/account/',
                    [
                        'key' => $key,
                        'userId' => $record['id'],
                        'email' => $record['email'],
                        'isNew' => $record->isNew()
                    ],
                    $record['email']
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