<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\users\clients\_nodes;

use DecodeLabs\R7\Legacy;
use DecodeLabs\Tagged as Html;
use df\arch;

class HttpChangePassword extends arch\node\Form
{
    protected $_auth;

    protected function init(): void
    {
        $this->_auth = $this->data->fetchOrCreateForAction(
            'axis://user/Auth',
            [
                'user' => $this->request['user'],
                'adapter' => 'Local'
            ],
            function ($auth) {
                $user = $this->data->fetchForAction(
                    'axis://user/Client',
                    $this->request['user']
                );

                $auth->import([
                    'user' => $user['id'],
                    'adapter' => 'Local',
                    'identity' => $user['email'],
                    'bindDate' => 'now'
                ]);
            }
        );
    }

    protected function getInstanceId(): ?string
    {
        return $this->_auth['#user'];
    }

    protected function createUi(): void
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Change password'));

        // User
        $fs->addField($this->_('User'))->push(
            Html::{'strong'}($this->_auth['user']['fullName'])
        );

        // New password
        $fs->addField($this->_('Password'))->push(
            $this->html->passwordTextbox('password', $this->values->password)
                ->isRequired(true)
        );

        // Confirm password
        $fs->addField($this->_('Confirm password'))->push(
            $this->html->passwordTextbox('confirmPassword', $this->values->confirmPassword)
                ->isRequired(true)
        );

        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function onSaveEvent()
    {
        $this->data->newValidator()
            ->addRequiredField('password')
                ->setMatchField('confirmPassword')
            ->validate($this->values);

        return $this->complete(function () {
            $this->_auth->password = Legacy::hash($this->values['password']);
            $this->_auth->save();

            $this->comms->flashSuccess(
                'password.change',
                $this->_('The user\'s password has been changed')
            );
        });
    }
}
