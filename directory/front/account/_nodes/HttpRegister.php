<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\front\account\_nodes;

use df\apex;
use df\arch;

use DecodeLabs\Disciple;
use DecodeLabs\R7\Legacy;

class HttpRegister extends arch\node\Form
{
    public const DEFAULT_ACCESS = arch\IAccess::GUEST;
    public const DEFAULT_EVENT = 'register';

    protected $_invite;

    protected function init()
    {
        if (Disciple::isLoggedIn()) {
            return Legacy::$http->defaultRedirect('account/');
        }

        if (isset($this->request['invite'])) {
            $this->_invite = $this->data->fetchForAction(
                'axis://user/Invite',
                ['key' => $this->request['invite']]
            );

            if (!$this->_invite['isActive']) {
                $this->comms->flashError(
                    'invite.inactive',
                    $this->_('The invite link you have followed is no longer active')
                );

                return Legacy::$http->defaultRedirect('/');
            }
        } else {
            if (!$this->data->user->config->isRegistrationEnabled()) {
                $this->comms->flashError(
                    'registration.disabled',
                    $this->_('Registration for this site is currently disabled')
                );

                return Legacy::$http->defaultRedirect('/');
            }
        }
    }

    public function getInvite()
    {
        return $this->_invite;
    }

    protected function getInstanceId()
    {
        if ($this->_invite) {
            return $this->_invite['key'];
        }

        return null;
    }

    protected function loadDelegates()
    {
        /**
         * RegisterLocal
         * @var apex\directory\front\account\_formDelegates\RegisterLocal $register
         */
        $register = $this->loadDelegate('Local', '~front/account/RegisterLocal');
        $register
            ->setInvite($this->_invite);
    }

    protected function createUi()
    {
        $this->view
            ->setTitle('Register for a new account')
            ->setCanonical('account/register');

        $this['Local']->renderUi();
    }

    protected function onRegisterEvent(...$args)
    {
        return $this['Local']->handleEvent('register', $args);
    }
}
