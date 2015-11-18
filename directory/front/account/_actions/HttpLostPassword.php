<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
use df\flow;

class HttpLostPassword extends arch\form\Action {

    const DEFAULT_ACCESS = arch\IAccess::GUEST;
    const DEFAULT_EVENT = 'send';

    protected function init() {
        if($this->user->isLoggedIn()) {
            return $this->http->defaultRedirect('account/');
        }
    }

    protected function createUi() {
        $this->content->push(
            $this->apex->component('~front/account/LostPassword', $this)
        );
    }

    protected function onSendEvent() {
        $client = null;
        $auth = null;

        $this->data->newValidator()
            ->addRequiredField('email')
                ->setCustomValidator(function($node, $value, $field) use (&$client) {
                    $client = $this->data->user->client->fetch()
                        ->where('email', '=', $value)
                        ->toRow();

                    if(!$client) {
                        $node->addError('incorrect', $this->_(
                            'This email address does not appear to be associated with an account'
                        ));
                    }
                })

            ->validate($this->values);
            ;

        if($client) {
            /*
            $auth = $this->data->user->auth->fetch()
                ->where('user', '=', $client)
                ->where('adapter', '=', 'Local')
                ->toRow();

            if(!$auth) {
                $this->values->email->addError('auth', $this->_(
                    'The account this email address is associated with does not use passwords'
                ));
            }
            */

            $count = $this->data->user->passwordResetKey->select()
                ->where('user', '=', $client)
                ->where('adapter', '=', 'Local')
                ->where('creationDate', '>', '-1 days')
                ->where('resetDate', '=', null)
                ->count();

            if($count >= 3) {
                $this->values->email->addError('limit', $this->_(
                    'The maximum number of password reset links have been sent for this account - please contact an admin for assistance'
                ));
            }
        }


        return $this->complete(function() use($client) {
            $key = $this->data->user->passwordResetKey->newRecord([
                    'user' => $client,
                    'adapter' => 'Local'
                ])
                ->generateKey()
                ->save();

            $this->comms->componentNotify('account/PasswordReset', [$key]);

            $this->comms->flashSuccess(
                'lostPassword.send',
                $this->_('A link has been sent to your email address with instructions on resetting your password')
            );
        });
    }
}