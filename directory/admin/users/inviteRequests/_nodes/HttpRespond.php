<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\inviteRequests\_nodes;

use DecodeLabs\Exceptional;

use DecodeLabs\Metamorph;
use DecodeLabs\Tagged as Html;
use df\arch;

class HttpRespond extends arch\node\Form
{
    protected $_request;

    protected function init(): void
    {
        $this->_request = $this->scaffold->getRecord();

        if (!$this->_request['isActive']) {
            throw Exceptional::Forbidden([
                'message' => 'Request is not active',
                'http' => 403
            ]);
        }
    }

    protected function getInstanceId(): ?string
    {
        return $this->_request['id'];
    }

    protected function createUi(): void
    {
        $this->content->addAttributeList($this->_request)
            ->addField('name')
            ->addField('email', function ($request) {
                return $this->html->mailLink($request['email']);
            })
            ->addField('companyName')
            ->addField('companyPosition')
            ->addField('creationDate', $this->_('Created'), function ($request) {
                return Html::$time->since($request['creationDate']);
            })
            ->addField('message', function ($request) {
                return Metamorph::text($request['message']);
            });


        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Invite response'));

        // Message
        $fs->addField($this->_('Message'))->setDescription($this->_(
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

    protected function onAcceptEvent()
    {
        $validator = $this->data->newValidator()
            ->addField('message', 'text')
            ->validate($this->values);

        return $this->complete(function () use ($validator) {
            if (!$this->_request['#user']) {
                $invite = $this->data->user->invite->newRecord([
                    'name' => $this->_request['name'],
                    'email' => $this->_request['email'],
                    'message' => $validator['message'],
                    'groups' => $this->_request['groups'] ?
                        $this->_request['groups']->toArray() : null
                ]);

                $invite->send();
                $this->_request['invite'] = $invite;
            } else {
                $this->comms->sendPreparedMail('account/InviteRequestAccept', [
                    'request' => $this->_request,
                    'message' => $validator['message']
                ]);
            }

            $this->_request['isActive'] = false;
            $this->_request->save();

            $this->mesh->emitEvent($this->_request, 'accept', [
                'message' => $validator['message']
            ]);

            if ($user = $this->_request['user']) {
                $user->groups->addList($this->_request['groups']->toArray());
                $user->save();
            }

            $this->comms->flashSuccess(
                'request.accept',
                $this->_('An invite has been sent in response to the request')
            );
        });
    }

    protected function onDenyEvent()
    {
        $validator = $this->data->newValidator()
            ->addField('message', 'text')
            ->validate($this->values);

        return $this->complete(function () use ($validator) {
            $this->_request['isActive'] = false;
            $this->_request->save();

            $this->mesh->emitEvent($this->_request, 'deny', [
                'message' => $validator['message']
            ]);

            $this->comms->sendPreparedMail('account/InviteRequestDeny', [
                'request' => $this->_request,
                'message' => $validator['message']
            ]);

            $this->comms->flashWarning(
                'request.deny',
                $this->_('The invite request has been denied')
            );
        });
    }
}
