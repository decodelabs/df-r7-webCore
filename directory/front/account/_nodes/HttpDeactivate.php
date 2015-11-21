<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\front\account\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpDeactivate extends arch\node\Form {

    const DEFAULT_ACCESS = arch\IAccess::CONFIRMED;
    const DEFAULT_EVENT = 'deactivate';

    protected $_deactivation;

    protected function init() {
        $this->_deactivation = $this->data->newRecord('axis://user/ClientDeactivation');
    }

    protected function createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('You really want to leave?'));

        $fs->addFlashMessage($this->_(
            'Are you sure you want to deactivate your account?'
        ), 'warning')
        ->setDescription($this->_(
            'You will no longer be able to log in to this site, and you will need to contact an admin to have your account reinstated!'
        ));

        $fs->addField($this->_('Why do you want to deactivate your account?'))->push(
            $this->html->textbox(
                    $this->fieldName('reason'),
                    $this->values->reason
                )
                ->setMaxLength(255)
        );

        $fs->addField($this->_('What could we have done better?'))->push(
            $this->html->textarea(
                    $this->fieldName('comments'),
                    $this->values->comments
                )
        );

        $fs->addButtonArea(
            $this->html->eventButton(
                    $this->eventName('deactivate'),
                    $this->_('Deactivate')
                )
                ->setIcon('remove')
                ->setDisposition('negative'),

            $this->html->cancelEventButton()
        );
    }

    protected function onDeactivateEvent() {
        $this->data->newValidator()
            ->addField('reason', 'text')
                ->setMaxLength(255)
            ->addField('reasonOther', 'text')
                ->setMaxLength(255)
                ->setRecordName('reason')
            ->addField('comments', 'text')

            ->validate($this->values)
            ->applyTo($this->_deactivation);

        if($this->isValid()) {
            $client = $this->data->user->client->fetchActive();
            $client->setAsDeactivated();
            $client->save();

            $this->_deactivation->user = $client;
            $this->_deactivation->save();

            $this->comms->componentAdminNotify(
                'users/Deactivation',
                [$this->_deactivation]
            );

            $this->user->logout();
            return $this->http->redirect('account/login');
        }
    }
}