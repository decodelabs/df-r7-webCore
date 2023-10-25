<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\account\_nodes;

use DecodeLabs\Disciple;
use DecodeLabs\Exceptional;
use DecodeLabs\R7\Config\Users as UserConfig;
use DecodeLabs\R7\Legacy;
use df\arch;

class HttpResetPassword extends arch\node\Form
{
    public const DEFAULT_ACCESS = arch\IAccess::GUEST;

    protected $_key;
    protected $_auth;

    protected function init(): void
    {
        if (Disciple::isLoggedIn()) {
            throw $this->_flashError('loggedIn', $this->_(
                'Passwords cannot be reset while you are logged in'
            ));
        }

        $this->_key = $this->data->user->passwordResetKey->fetch()
            ->where('user', '=', $this->request['user'])
            ->where('key', '=', $this->request['key'])
            ->toRow();

        if (!$this->_key) {
            throw $this->_flashError('notFound', $this->_(
                'The password reset key this link refers to no longer exists'
            ));
        }

        if (!$user = $this->_key['user']) {
            throw Exceptional::UnexpectedValue([
                'message' => 'Client not attached to key',
            ]);
        }

        if ($this->_key->isRedeemed()) {
            throw $this->_flashError('alreadyReset', $this->_(
                'The password reset key this link refers to has already been redeemed'
            ));
        }

        if ($this->_key['adapter'] != 'Local') {
            throw Exceptional::{'df/user/authentication/Forbidden'}([
                'message' => 'Password reset key not for local adapter',
                'http' => 403
            ]);
        }

        if ($this->_key->hasExpired()) {
            throw $this->_flashError('expired', $this->_(
                'The password reset key this link refers to has now expired'
            ));
        }

        $this->_auth = $this->data->fetchOrCreateForAction(
            'axis://user/Auth',
            [
                'user' => $user['id'],
                'adapter' => 'Local'
            ],
            function ($auth) use ($user) {
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
        return null;
    }

    public function getKey()
    {
        return $this->_key;
    }

    protected function _flashError($flashKey, $message)
    {
        $this->comms->flashError(
            'passwordResetKey.' . $flashKey,
            $message
        );

        return Legacy::$http->redirectNow('account/');
    }

    protected function setDefaultValues(): void
    {
        $this->data->user->passwordResetKey->pruneUnusedKeys();
    }

    protected function createUi(): void
    {
        $this->view
            ->setTitle('Reset your password')
            ->setCanonical('account/reset-password')
            ->canIndex(false);

        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Reset password'));

        // Email
        $fs->addField($this->_('Email'))->push(
            $this->html->emailTextbox(
                $this->fieldName('email'),
                $this->_key['user']['email']
            )
                ->isDisabled(true)
        );

        // New password
        $fs->addField($this->_('New password'))->push(
            $this->html->passwordTextbox(
                $this->fieldName('newPassword'),
                $this->values->newPassword
            )
                ->isRequired(true)
        );

        // Confirm password
        $fs->addField($this->_('Confirm new password'))->push(
            $this->html->passwordTextbox(
                $this->fieldName('confirmNewPassword'),
                $this->values->confirmNewPassword
            )
                ->isRequired(true)
        );

        // Buttons
        $fs->addDefaultButtonGroup();
    }

    protected function onSaveEvent()
    {
        $userConfig = UserConfig::load();

        $this->data->newValidator()
            ->addRequiredField('newPassword', 'password')
                ->setMatchField('confirmNewPassword')
                ->shouldCheckStrength($userConfig->shouldCheckPasswordStrength())
                ->setMinStrength($userConfig->getMinPasswordStrength())

            ->validate($this->values);


        return $this->complete(function () {
            $this->_auth->password = $this->user->password->hash($this->values['newPassword']);
            $this->_auth->save();

            $this->_key->resetDate = 'now';
            $this->_key->save();

            $this->data->user->passwordResetKey->deleteRecentUnusedKeys($this->_key);

            $this->comms->flashSuccess(
                'password.reset',
                $this->_('Your password has been updated')
            );

            return 'account/';
        });
    }
}
