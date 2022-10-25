<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\account\_nodes;

use df\arch;

use DecodeLabs\Disciple;

class HttpConfirmLogin extends arch\node\Form
{
    public const DEFAULT_ACCESS = arch\IAccess::BOUND;
    public const DEFAULT_EVENT = 'login';
    public const DEFAULT_REDIRECT = '/';

    protected function getInstanceId(): ?string
    {
        return null;
    }

    protected function createUi(): void
    {
        $this->view
            ->setCanonical('account/confirm-password')
            ->canIndex(false);

        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Confirm password'));

        // Identity
        $fs->addField($this->_('User'))
            ->addEmailTextbox(
                    $this->fieldName('name'),
                    Disciple::getFullName()
                )
                ->isDisabled(true);

        // Password
        $fs->addField($this->_('Password'))
            ->addPasswordTextbox(
                    $this->fieldName('password'),
                    $this->values->password
                )
                ->isRequired(true);

        // Buttons
        $fs->addButtonArea()->push(
            $this->html->eventButton(
                    $this->eventName('login'),
                    $this->_('Sign in')
                )
                ->setIcon('accept'),

            $this->html->cancelEventButton()
                ->setEvent('logout')
        );
    }

    protected function onLoginEvent()
    {
        if (!$this->values->password->hasValue()) {
            $this->values->password->addError('required', $this->_(
                'Please enter your password'
            ));
        }

        return $this->complete(function () {
            $result = $this->user->auth->bind(
                $this->user->auth->newRequest('Local')
                    ->setIdentity(Disciple::getEmail())
                    ->setCredential('password', $this->values['password'])
            );

            if (!$result->isValid()) {
                $this->values->password->addError('invalid', $this->_(
                    'The password entered was incorrect'
                ));

                return false;
            }

            return 'account/';
        });
    }

    protected function onLogoutEvent()
    {
        return $this->complete(function () {
            return 'account/logout';
        });
    }
}
