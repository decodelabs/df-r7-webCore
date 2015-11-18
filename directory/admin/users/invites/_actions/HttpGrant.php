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

class HttpGrant extends arch\form\Action {

    const DEFAULT_EVENT = 'saveUsers';

    protected function loadDelegates() {
        $this->loadDelegate('users', '../clients/UserSelector')
            ->isForMany(true)
            ->isRequired(true);
    }

    protected function setDefaultValues() {
        $this->values->allowance = $this->data->user->config->getInviteCap();
    }

    protected function createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Specific users'));

        // Allowance
        $fs->addField($this->_('Set allowance to'))->push(
            $this->html->numberTextbox('allowance', $this->values->allowance)
                ->isRequired(true)
                ->setMin(1)
        );

        // Users
        $fs->addField($this->_('Users'))->push($this['users']);

        // Buttons
        $fs->addDefaultButtonGroup('saveUsers');


        if(!isset($this->request['user'])) {
            $form = $this->content->addForm();
            $fs = $form->addFieldSet($this->_('All users'));

            $fs->addFlashMessage($this->_(
                'Please note, this action will only affect users who have initiated their invite allowance upon sending their first invitation'
            ));

            $fs->addField($this->_('Reset allowance to'))->push(
                $this->html->numberTextbox('allowance', $this->values->allowance)
                    ->isRequired(true)
                    ->setMin(1)
            );

            $fs->addDefaultButtonGroup('saveAll');
        }
    }

    protected function onSaveUsersEvent() {
        $validator = $this->data->newValidator()
            ->addRequiredField('allowance', 'integer')
                ->setMin(1)
            ->addField('users', 'delegate')
                ->fromForm($this)
            ->validate($this->values);


        return $this->complete(function() use($validator) {
            $this->data->user->invite->grantAllowance($validator['users'], $validator['allowance']);
            $this->user->instigateGlobalKeyringRegeneration();

            $this->comms->flashSuccess(
                'invite.allowance',
                $this->_('User invite allowances have been successfully updated')
            );
        });
    }

    protected function onSaveAllEvent() {
        $validator = $this->data->newValidator()
            ->addRequiredField('allowance', 'integer')
                ->setMin(1)
            ->validate($this->values);


        return $this->complete(function() use($validator) {
            $this->data->user->invite->grantAllAllowance($validator['allowance']);
            $this->user->instigateGlobalKeyringRegeneration();

            $this->comms->flashSuccess(
                'invite.allowance',
                $this->_('User invite allowances have been successfully updated')
            );
        });
    }
}