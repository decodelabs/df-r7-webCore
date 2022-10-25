<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\account\_formDelegates;

use df;
use df\core;
use df\apex;
use df\arch;
use df\user;

use DecodeLabs\Disciple;
use DecodeLabs\Dictum;
use DecodeLabs\Tagged as Html;

class LoginLocal extends arch\node\form\Delegate implements arch\node\IParentUiHandlerDelegate
{
    use arch\node\TForm_ParentUiHandlerDelegate;

    public const DEFAULT_REDIRECT = '/';

    protected function createUi(): void
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Sign-in'));

        // Register
        if ($this->data->user->config->isRegistrationEnabled()) {
            $fs->addField()->push(
                Html::{'p'}([
                    $this->_('Not signed up yet?'), ' ',
                    $this->html->link(
                        $this->uri('account/register'),
                        $this->_('Register now...')
                    )
                ])
            );
        }

        // Identity
        $fs->addField($this->_('Email address'))
            ->addEmailTextbox(
                    $this->fieldName('identity'),
                    $this->values->identity
                )
                ->isRequired(true);

        // Password
        $fs->addField($this->_('Password'))
            ->addPasswordTextbox(
                    $this->fieldName('password'),
                    $this->values->password
                )
                ->shouldAutoComplete(false)
                ->isRequired(true);

        // Remember
        $fs->addField()->push(
            $this->html->checkbox(
                $this->fieldName('rememberMe'),
                $this->values->rememberMe,
                $this->_('Remember me')
            )
        );

        // Buttons
        $fs->addButtonArea()->push(
            $this->html->eventButton(
                    $this->eventName('login'),
                    $this->_('Sign in')
                )
                ->setIcon('user'),

            $this->html->cancelEventButton(),

            $this->html->buttonGroup(
                $this->html->link(
                    $this->uri('account/lost-password', true),
                    $this->_('Forgot your password?')
                )
            )
        );
    }

    protected function onLoginEvent()
    {
        if (!$this->values->identity->hasValue()) {
            $this->values->identity->addError('required', $this->_(
                'Please enter your username'
            ));
        }

        if (!$this->values->password->hasValue()) {
            $this->values->password->addError('required', $this->_(
                'Please enter your password'
            ));
        }


        return $this->complete(function () {
            $gateKeeper = Disciple::getGateKeeper();
            $identity = (string)$this->values['identity'];

            // Check approval
            if (!$gateKeeper->approveLogin($identity, function ($time) {
                $this->values->identity->addError('time', $this->_(
                    'Too many login attempts, please wait '.Dictum::$time->until($time)
                ));

                $this->values->password->setValue('');
            })) {
                return false;
            }


            // Run login
            $result = $this->user->auth->bind(
                $this->user->auth->newRequest('Local')
                    ->setIdentity($this->values['identity'])
                    ->setCredential('password', $this->values['password'])
                    ->setAttribute('rememberMe', (bool)$this->values['rememberMe'])
            );


            // Log attempt
            $gateKeeper->reportLogin($identity, $result->isValid());


            // Deal with errors
            if (!$result->isValid()) {
                if ($result->getCode() === $result::NO_STATUS) {
                    $this->values->identity->addError('status', $this->_(
                        'This account is currently disabled'
                    ));
                } else {
                    $this->values->identity->addError('invalid', $this->_(
                        'The email address or password entered was incorrect'
                    ));
                }

                $this->values->password->setValue('');
                return false;
            }
        });
    }
}
