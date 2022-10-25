<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\account\_nodes;

use df\arch;

use DecodeLabs\Disciple;

class HttpChangePassword extends arch\node\Form
{
    public const DEFAULT_ACCESS = arch\IAccess::CONFIRMED;

    protected $_auth;

    protected function init(): void
    {
        $this->_auth = $this->data->fetchOrCreateForAction(
            'axis://user/Auth',
            [
                'user' => Disciple::getId(),
                'adapter' => 'Local'
            ],
            function ($auth) {
                $auth->import([
                    'user' => Disciple::getId(),
                    'adapter' => 'Local',
                    'identity' => Disciple::getEmail(),
                    'bindDate' => 'now'
                ]);
            }
        );
    }

    protected function getInstanceId(): ?string
    {
        return null;
    }

    public function getAuth()
    {
        return $this->_auth;
    }

    protected function createUi(): void
    {
        $this->view
            ->setCanonical('account/change-password')
            ->canIndex(false);

        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Change password'));

        // Old password
        if (!$this->_auth->isNew()) {
            $fs->addField($this->_('Old password'))->push(
                $this->html->passwordTextbox(
                        $this->fieldName('oldPassword'),
                        $this->values->oldPassword
                    )
                    ->isRequired(true)
            );
        }

        // New password
        $fs->addField($this->_('New password'))->push(
            $this->html->passwordTextbox(
                    $this->fieldName('newPassword'),
                    $this->values->newPassword
                )
                ->isRequired(true)
        );

        // Confirm new password
        $fs->addField($this->_('Confirm new password'))->push(
            $this->html->passwordTextbox(
                    $this->fieldName('confirmNewPassword'),
                    $this->values->confirmNewPassword
                )
                ->isRequired(true)
        );

        // Buttons
        $fs->addDefaultButtonGroup('save', $this->_('Update password'));
    }

    protected function onSaveEvent()
    {
        $userConfig = $this->data->user->config;

        $validator = $this->data->newValidator()

            // Old password
            ->chainIf(!$this->_auth->isNew(), function ($validator) {
                $validator->addRequiredField('oldPassword', 'text')
                    ->extend(function ($value, $field) {
                        $hash = $this->user->password->hash($value);

                        if ($hash != $this->_auth['password']) {
                            $field->addError('incorrect', $this->_(
                                'This password is incorrect'
                            ));
                        }
                    });
            })

            // New password
            ->addRequiredField('newPassword', 'password')
                ->setMatchField('confirmNewPassword')
                ->shouldCheckStrength($userConfig->shouldCheckPasswordStrength())
                ->setMinStrength($userConfig->getMinPasswordStrength())

            ->validate($this->values);

        $this->values->oldPassword->setValue('');
        $this->values->newPassword->setValue('');
        $this->values->confirmNewPassword->setValue('');

        return $this->complete(function () use ($validator) {
            $this->_auth->password = $validator['newPassword'];
            $this->_auth->save();
            $this->user->refreshClientData();

            $this->comms->flashSuccess(
                'password.change',
                $this->_('Your password has been successfully updated')
            );
        });
    }
}
