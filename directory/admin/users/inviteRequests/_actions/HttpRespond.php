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
    
class HttpRespond extends arch\form\Action {

    protected $_request;

    protected function init() {
        $this->_request = $this->scaffold->getRecord();

        if(!$this->_request['isActive']) {
            $this->throwError(403, 'Request is not active');
        }
    }

    protected function getInstanceId() {
        return $this->_request['id'];
    }

    protected function createUi() {
        $this->content->addAttributeList($this->_request)
            ->addField('name')
            ->addField('email', function($request) {
                return $this->html->mailLink($request['email']);
            })
            ->addField('companyName')
            ->addField('companyPosition')
            ->addField('creationDate', $this->_('Created'), function($request) {
                return $this->html->timeSince($request['creationDate']);
            })
            ->addField('message', function($request) {
                return $this->html->plainText($request['message']);
            });


        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Invite response'));

        // Message
        $fs->addFieldArea($this->_('Message'))->setDescription($this->_(
            'If you enter a message below an email will be sent to the requestee'
        ))->push(
            $this->html->textarea('message', $this->values->message)
        );

        // Buttons
        $fs->addButtonArea(
            $this->html->saveEventButton('accept', $this->_('Accept'))
                ->setIcon('accept'),

            $this->html->saveEventButton('deny', $this->_('Deny'))
                ->setIcon('deny')
                ->setDisposition('negative'),

            $this->html->cancelEventButton()
        );
    }

    protected function onAcceptEvent() {
        $validator = $this->data->newValidator()
            ->addField('message', 'text')
            ->validate($this->values);

        return $this->complete(function() use($validator) {
            $invite = $this->data->user->invite->newRecord([
                'name' => $this->_request['name'], 
                'email' => $this->_request['email'],
                'message' => $validator['message']
            ]);

            $invite->send();

            $this->_request['isActive'] = false;
            $this->_request['invite'] = $invite;
            $this->_request->save();

            $this->comms->flashSuccess(
                'request.accept',
                $this->_('An invite has been sent in response to the request')
            );
        });
    }

    protected function onDenyEvent() {
        $validator = $this->data->newValidator()
            ->addField('message', 'text')
            ->validate($this->values);

        return $this->complete(function() use($validator) {
            $this->_request['isActive'] = false;
            $this->_request->save();

            if($validator['message']) {
                $this->comms->componentNotify(
                    'users/InviteRequestDeny',
                    [$this->_request, $validator['message']]
                );
            }

            $this->comms->flashWarning(
                'request.deny',
                $this->_('The invite request has bene denied')
            );
        });
    }
}