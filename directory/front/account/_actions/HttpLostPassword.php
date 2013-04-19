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
    
class HttpLostPassword extends arch\form\Action {

    const DEFAULT_ACCESS = arch\IAccess::GUEST;
    const DEFAULT_EVENT = 'update';

    protected function _init() {
        if($this->user->isLoggedIn()) {
            return $this->http->redirect('account/');
        }
    }

    protected function _createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Password recovery'));

        $fs->push(
            $this->html->notification($this->_(
                'Please enter your email address and you will be sent a link with instructions on resetting your password'
            ))
        );

        // Email
        $fs->addFieldArea($this->_('Email address'))->push(
            $this->html->emailTextbox('email', $this->values->email)
                ->isRequired(true)
        );

        // Buttons
        $fs->addButtonArea()->push(
            $this->html->eventButton(
                    $this->eventName('update'),
                    $this->_('Update')
                )
                ->setIcon('mail')
                ->setDisposition('positive'),

            $this->html->cancelEventButton()
        );
    }

    protected function _onUpdateEvent() {
        $client = null;
        $auth = null;

        $this->data->newValidator()
            ->addField('email', 'email')
                ->isRequired(true)
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
                ->end()

            ->validate($this->values);
            ;

        if($client) {
            $auth = $this->data->user->auth->fetch()
                ->where('user', '=', $client)
                ->where('adapter', '=', 'Local')
                ->toRow();

            if(!$auth) {
                $this->values->email->addError('auth', $this->_(
                    'The account this email address is associated with does not use passwords'
                ));
            }

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

        if($this->isValid()) {
            $key = $this->data->user->passwordResetKey->newRecord([
                    'user' => $client,
                    'adapter' => 'Local'
                ])
                ->generateKey()
                ->save()
                ;

            $html = $this->aura->getView('emails/PasswordReset.html')
                ->setArg('key', $key)
                ->shouldRenderBase(false)
                ->setLayout('Blank')
                ->render();

            $mail = new core\mail\Message();
            $mail->setSubject($this->_('Password reset'));
            $mail->addToAddress($client['email'], $client['fullName']);
            $mail->setBodyHtml($html);
            $mail->send();

            $this->comms->notify(
                'lostPassword.send',
                $this->_('The link has been sent to your email address - please follow its instructions in the next 48 hours'),
                'success'
            );

            return $this->complete();
        }
    }
}