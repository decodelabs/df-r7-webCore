<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\account\_nodes;

use df\arch;

use DecodeLabs\Disciple;
use DecodeLabs\R7\Legacy;

class HttpAccessPass extends arch\node\Form
{
    public const DEFAULT_EVENT = 'login';
    public const DEFAULT_ACCESS = arch\IAccess::GUEST;

    protected $_pass;

    protected function init()
    {
        $this->_pass = $this->data->fetchForAction(
            'axis://user/AccessPass',
            $this->request['pass']
        );

        $error = false;

        if (!$this->_pass['user']) {
            $error = true;

            $this->comms->flashWarning($this->_(
                'The access pass you are trying to use is no longer valid'
            ));
        } elseif ($this->_pass['expiryDate']->isPast()) {
            $error = true;

            $this->comms->flashWarning($this->_(
                'The access pass you are trying to use has expired'
            ));
        }

        if ($error) {
            $this->_pass->delete();
            return Legacy::$http->defaultRedirect('/');
        }

        if (Disciple::isLoggedIn()) {
            $this->user->auth->unbind();
            return Legacy::$http->redirect();
        }
    }

    protected function getInstanceId()
    {
        return $this->_pass['id'];
    }

    protected function createUi()
    {
        $this->view->canIndex(false);

        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Sign in with access pass'));

        // Email
        $fs->addField($this->_('Email for this account'))->push(
            $this->html->emailTextbox('email', $this->values->email)
                ->isRequired(true)
        );

        // Buttons
        $fs->addButtonArea(
            $this->html->eventButton(
                    $this->eventName('login'),
                    $this->_('Sign in')
                )
                ->setIcon('user'),

            $this->html->cancelEventButton()
        );
    }

    protected function onLoginEvent()
    {
        $validator = $this->data->newValidator()

            // Email
            ->addRequiredField('email')
            ->validate($this->values);

        if (!$validator->isValid()) {
            return;
        }

        if ($validator['email'] !== $this->_pass['user']['email']) {
            $this->values->email->addError('incorrect', $this->_(
                'This is not the email address associated with this account'
            ));
        }

        if (!$this->isValid()) {
            return;
        }

        if (!$this->user->auth->bindDirect($this->_pass['#user'], true)) {
            $this->comms->flashWarning($this->_(
                'This account is not available for sign in'
            ));

            return;
        }

        return $this->complete(function () {
            $this->_pass->delete();
            return '/';
        });
    }
}
