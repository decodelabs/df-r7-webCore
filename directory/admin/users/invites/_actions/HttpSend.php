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
    
class HttpSend extends arch\form\Action {

    const DEFAULT_EVENT = 'send';

    protected $_invite;

    protected function _init() {
        $this->data->checkAccess('axis://user/Invite', 'add');
        $this->_invite = $this->data->newRecord('axis://user/Invite');
    }

    protected function _setupDelegates() {
        $this->loadDelegate('groups', '~admin/users/groups/GroupSelector');
    }

    protected function _createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('User details'));

        // Name
        $fs->addFieldArea($this->_('Name'))->push(
            $this->html->textbox('name', $this->values->name)
                ->isRequired(true)
        );

        // Email
        $fa = $fs->addFieldArea($this->_('Email address'))->push(
            $this->html->emailTextbox('email', $this->values->email)
                ->isRequired(true)
        );

        if($this->values->email->hasError('inviteExists')) {
            $fa->push(
                $this->html('<br />'),

                $this->html->checkbox('sendAnyway', $this->values->sendAnyway, $this->_(
                    'Resend invite anyway'
                ))
            );
        }

        // Message
        $fs->addFieldArea($this->_('Message'))->push(
            $this->html->textarea('message', $this->values->message)
        );

        // Groups
        $fs->push($this->getDelegate('groups')->renderFieldArea($this->_('Registration groups')));

        // Buttons
        $fs->push($this->html->defaultButtonGroup('send', $this->_('Send')));
    }

    protected function _onSendEvent() {
        $this->_invite->groups = $this->getDelegate('groups')->apply();

        $this->data->newValidator()

            // Name
            ->addField('name', 'text')
                ->isRequired(true)
                ->end()

            // Email
            ->addField('email', 'email')
                ->setCustomValidator(function($node, $value) {
                    if($this->data->user->client->emailExists($value)) {
                        $node->addError('userExists', $this->_(
                            'A user has already registered with this email address'
                        ));
                    }

                    if($this->data->user->invite->emailIsActive($value) && !$this->format->stringToBoolean($this->values['sendAnyway'])) {
                        $node->addError('inviteExists', $this->_(
                            'An invite already exists for this email address'
                        ));
                    }
                })
                ->isRequired(true)
                ->end()

            // Message
            ->addField('message', 'text')
                ->end()

            ->validate($this->values)
            ->applyTo($this->_invite);


        if($this->isValid()) {
            $this->_invite['isFromAdmin'] = true;
            $this->_invite->send();

            $this->comms->flash(
                'invite.send',
                $this->_('Your invite has been successfully sent'),
                'success'
            );

            return $this->complete();
        }
    }
}