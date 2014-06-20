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

    protected function _init() {
        $this->_request = $this->data->fetchForAction(
            'axis://user/InviteRequest',
            $this->request->query['request'],
            'respond'
        );

        if(!$this->_request['isActive']) {
            $this->throwError(403, 'Request is not active');
        }
    }

    protected function _getDataId() {
        return $this->_request['id'];
    }

    protected function _setDefaultValues() {
        $this->values->type = 'buyer';
    }

    protected function _createUi() {
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
        $fs = $form->addFieldSet($this->_('Accept request'));

        // Type
        $fs->addFieldArea($this->_('Profile type'))->setDescription($this->_(
            'By accepting this request, a personal invite will be sent for the requestee to follow and register with a profile type as specified below'
        ))->push(
            $this->html->radioButtonGroup('type', $this->values->type, [
                    'buyer' => $this->_('Buyer'),
                    'provider' => $this->_('Provider')
                ])
                ->isRequired(true)
        );

        // Buttons
        $fs->addButtonArea(
            $this->html->saveEventButton('accept', $this->_('Accept'))
                ->setIcon('accept'),

            $this->html->cancelEventButton()
        );



        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Deny request'));

        // Message
        $fs->addFieldArea($this->_('Message'))->setDescription($this->_(
            'If you enter a message below an email will be sent to the requestee detailing your reasons for denying their request'
        ))->push(
            $this->html->textarea('message', $this->values->message)
        );

        // Buttons
        $fs->addButtonArea(
            $this->html->saveEventButton('deny', $this->_('Deny'))
                ->setIcon('deny')
                ->setDisposition('negative'),

            $this->html->cancelEventButton()
        );
    }

    protected function _onAcceptEvent() {
        $validator = $this->data->newValidator()
            ->addField('type', 'enum')
                ->setOptions(['buyer', 'provider'])
                ->isRequired(true)
                ->end()

            ->validate($this->values);

        if($this->isValid()) {
            $invite = $this->data->user->invite->newRecord([
                'name' => $this->_request['name'], 
                'email' => $this->_request['email'],
                'customData' => ['profileType' => $validator['type']]
            ]);

            if($validator['type'] == 'buyer') {
                $invite->groups = [2];

                $invite->send(
                    'emails/BuyerPersonalInvitation.html',
                    '~front/account/'
                );
            } else if($validator['type'] == 'provider') {
                $invite->groups = [3];

                $invite->send(
                    'emails/ProviderPersonalInvitation.html',
                    '~front/account/'
                );
            }

            $this->_request['isActive'] = false;
            $this->_request['invite'] = $invite;
            $this->_request->save();

            $this->comms->flash(
                'request.accept',
                $this->_('An invite has been sent in response to the request'),
                'success'
            );

            return $this->complete();
        }
    }

    protected function _onDenyEvent() {
        $validator = $this->data->newValidator()
            ->addField('message', 'text')
                ->end()

            ->validate($this->values);

        if($this->isValid()) {
            $this->_request['isActive'] = false;
            $this->_request->save();

            if($validator['message']) {
                $this->comms->templateNotify(
                    'emails/InviteRequestDeny.html',
                    '~front/account/',
                    ['request' => $this->_request, 'message' => $validator['message']],
                    $this->_request['email']
                );
            }

            $this->comms->flash(
                'request.deny',
                $this->_('The invite request has bene denied'),
                'warning'
            );

            return $this->complete();
        }
    }
}